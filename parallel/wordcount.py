import sys
import collections
from mpi4py import MPI
import numpy as np
import math

input_string = "abracadabraarbadacarba"
num_rules = 0

#input_file = "books/ParadiseLost.txt"
input_file = "books/KingJamesBible.txt"

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

    return master_wc


def main():
    
    comm = MPI.COMM_WORLD
    rank = comm.Get_rank()

    # master process will start the slaves
    if rank == 0:
        start_time = MPI.Wtime()
        wordcount = master(comm)
        end_time = MPI.Wtime()
        print wordcount
        print "Time: %f secs" % (end_time - start_time)
    else:
        slave(comm)
    
if __name__ == "__main__":
    main()
