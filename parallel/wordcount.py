import sys
import collections
from mpi4py import MPI
import numpy as np
import math
import time

input_string = "abracadabraarbadacarba"
num_rules = 0

#input_file = "books/ParadiseLost.txt"
#input_file = "books/KingJamesBible.txt"
input_file = "books/hobbit_wiki.txt"

def slave(comm):

    rank = comm.Get_rank()
    # need status updates
    status = MPI.Status()

    wc_dict = {}

    while True:
        # receive the job
        line = comm.recv(source = 0, tag = MPI.ANY_TAG, status = status)

        if status.Get_tag() == 0:
            # if we get the dietag, end the slave process and return the word counts
            comm.send(wc_dict, dest=0, tag=rank)
            return
        
        wordlist = line.split()
        for word in wordlist:
            #strip last char if not in A-Z,a-z,0-9
            x = ord(word[-1:])
            if x < 48 or x > 57 or x < 65 or x > 90 or x < 97 or x > 122:
                word = word[:-1]
            if not word in wc_dict:
                wc_dict[word] = 1
            else:
                wc_dict[word] += 1

        comm.send(True,dest=0,tag=rank)

def master(comm):
    rank = comm.Get_rank()
    size = comm.Get_size()

    master_wc = {}
    status = MPI.Status()

    f = open(input_file, 'r')
    count = 1

    for line in f:
        # seed slaves with initial jobs
        if (count < size):
            comm.send(line,dest = count,tag = 1)
            count += 1
        # get result from slave and send next job
        else:
            # if job is done, we'll receive a True flag
            slaveflag = comm.recv(source = MPI.ANY_SOURCE, tag = MPI.ANY_TAG, status=status)
            if slaveflag:
                # send a new job if there are any left
                comm.send(line,dest=status.Get_source(),tag=1)

    # no more jobs, so we need to collect all results
    for n in xrange(1,size):
        slaveflag = comm.recv(source = MPI.ANY_SOURCE, tag = MPI.ANY_TAG, status = status)
        comm.send(None, dest=status.Get_source(), tag = 0)

    # callback the wc_dicts
    for n in xrange(1,size):
        slave_wc = comm.recv(source = MPI.ANY_SOURCE, tag = MPI.ANY_TAG, status = status)
        print "received from: " + str(status.Get_source())
        for k,v in slave_wc.items():
            if not k in master_wc:
                master_wc[k] = v
            else:
                master_wc[k] += v

    f.close()
    return master_wc

def parallel_sequitur(full_list, comm, rules):
    rank = comm.Get_rank()
    size = comm.Get_size()

    #print str(rank) + "has rules:" + str(rules)

    if rank == 0:
        # compute list size for children
        blocksize = len(full_list)/size
        for i in xrange(1,size):
            data_block = full_list[blocksize*i:blocksize*(i+1)]
            comm.send(data_block, dest = i)
        data_block = full_list[0:blocksize]
    else:
        # all processes will receive data
        received = comm.recv(source = 0)
        data_block = received

    # apply rules to set
    for i in range(len(data_block)):
        if data_block[i] in rules:
            data_block[i] = rules[data_block[i]]
    #for word in data_block:
    #    if word in rules:
    #        word = rules[word]

    cummulative_text = []
    # need to send all data back to rank 0
    if rank != 0:
        comm.send(data_block, dest=0)
    else:
        # rank 0 should append its own data
        cummulative_text.append(data_block)
        # rank 0 should receive all data
        for i in xrange(1,size):
            received = comm.recv(source=i)
            print "received compressed from rank: " + str(i)
            #print "from rank: " + str(i) + " received: " + str(received)
            cummulative_text.append(received)

    return cummulative_text

def main():
    
    comm = MPI.COMM_WORLD
    rank = comm.Get_rank()
    size = comm.Get_size()

    #stime = time.time()
    # master process will start the slaves
    if rank == 0:
        start_time = MPI.Wtime()
        wordcount = master(comm)
        end_time = MPI.Wtime()
       # print wordcount
        print "Time: %f secs" % (end_time - start_time)
    else:
        slave(comm)
 
    rule_num = 1
    # used for faster replacing
    wordtoruleset = {}
    # used for decoding/output
    ruletowordset = {}
    # some vars for stats
    totalchars = 0
    totalrulesize = 0

    rulestomake = []

    # some optimizations
    # prune the count dictionary. remove all items smaller than 3 chars
    # also, reorder. give smallest words the smallest rule names
    if rank == 0:
         # get all word we want from dict and put them in a list
        for k,v in wordcount.items():
            if v > 1 and len(k) > 2:
                # add word to list
                rulestomake.append(k)
        # sort the words we want by length
        rulestomake.sort(key=len)
        #print rulestomake
    stime = MPI.Wtime()
    # generate the rules from the count dictionary
    if rank == 0:
        for item in rulestomake:
            rule = '@' + str(rule_num) + '@'
            wordtoruleset[item] = rule
            ruletowordset[rule] = item
            rule_num += 1
            totalchars += len(k)
            totalrulesize += len(rule)
        #print wordtoruleset
        print "Total Chars: " + str(totalchars)
        print "Total Rule Size: " + str(totalrulesize)
        # calculate our rough stats. not very meaningful unless we analyze all differences
        ruleset_compression = (totalchars - totalrulesize)/(float(totalchars))
        print "Compression of %f if all rules used once" % (ruleset_compression*100)
    """
    # generate the rules from the count dictionary
    if rank == 0:
        for k, v in wordcount.items():
            if v > 1:
                rule = '@' + str(rule_num) + '@'
                wordtoruleset[k] = rule
                ruletowordset[rule] = k
                rule_num += 1
                totalchars += len(k)
                totalrulesize += len(rule)
        #print wordtoruleset
        print "Total Chars: " + str(totalchars)
        print "Total Rule Size: " + str(totalrulesize)
        # calculate our rough stats. not very meaningful unless we analyze all differences
        ruleset_compression = (totalchars - totalrulesize)/(float(totalchars))
        print "Compression of %f if all rules used once" % (ruleset_compression*100)
    """ 
     
    full_list = []
    
    # rank 0 will get the input
    if rank == 0:
        f = open(input_file,'r')
        filebuf = f.read()
        #print filebuf
        full_list = filebuf.split()
        #print full_list
        # send rules to other processes
        for i in xrange(1, size):
            comm.send(wordtoruleset,dest = i)
    else:
        # receive the rules
        wordtoruleset = comm.recv(source = 0)

    comm.barrier()
    # run a basic concat-sequitur
    compressed_text = parallel_sequitur(full_list,comm,wordtoruleset)
    comm.barrier()

    if rank == 0:
        mastercompressedstr = ''
        # combine all compressions together
        for x in compressed_text:
            mastercompressedstr += ' '.join(map(str,x)) + " "
        #print mastercompressedstr
        etime = MPI.Wtime()
        print "WC Total Time: %f" % (etime - stime)

        # save to file
        out = open("wcOUT.txt","w")
        out.write(mastercompressedstr + '\n')
        for k,v in ruletowordset.items():
            out.write(k+": " + v + '\n')
        out.close()
       #print compressed_text
       #pass

        

if __name__ == "__main__":
    main()
