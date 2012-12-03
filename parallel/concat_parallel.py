import sys
import collections
from mpi4py import MPI
import numpy as np
import math

input_string = "abracadabraarbadacarba"
num_rules = 0

def digram_utility(rules_so_far):
    # find last digram created
    last_digram = rules_so_far['0'][-2:]
    count = 0
    # count how many times it appears in the grammar beforehand
    for key in rules_so_far.keys():
        count += rules_so_far[key].count(last_digram)
    #print count
    if count == 1:
        # if exactly once, done!
        return True, rules_so_far
    else:
        # otherwise, create a new rule for this digram
        global num_rules
        num_rules += 1
        for x in rules_so_far.keys():
            rules_so_far[x] = rules_so_far[x].replace(last_digram, str(num_rules))
            rules_so_far[str(num_rules)] = last_digram
        return False, rules_so_far

# check for rule utility

def rule_utility(rules_so_far):
    for rule in [a for a in rules_so_far.keys() if a <> '0']:
        # count how many times each rule besides the main string is used
        count = 0
        other_keys = [a for a in rules_so_far.keys() if rule <> a]
        for key in other_keys:
            count += rules_so_far[key].count(rule)
        # if rule is only used once, consolidate
        if count < 2:
            for key in other_keys:
                rules_so_far[key] = rules_so_far[key].replace(rule, rules_so_far[rule])
            del rules_so_far[rule]
            return False, rules_so_far
    return True, rules_so_far

# test cases
def make_test():
    rules = {}
    rules['0'] = '22'
    rules['1'] = 'aa'
    rules['2'] = '1b'
    return rules

def run_sequitur(strblock):
    
    # set up initial params
    rules = {}
    rules['0'] = ''
    digram_bool = False
    rule_bool = False

    for x in range(len(strblock)):
      digram_bool = False
      rule_bool = False
      string_so_far = strblock[:x]
      rules['0'] = rules['0'] + strblock[x]
      while not digram_bool:
        digram_bool, rules = digram_utility(rules)
      while not rule_bool:
        rule_bool, rules = rule_utility(rules)
  
    return rules

def parallel_sequitur(data, comm):
    rank = comm.Get_rank()
    size = comm.Get_size()

    # store rules from each process
    rules = {}

    # need this to store all results after parallel is done
    cummulative_ruleset = []

    if rank == 0:
      # string size for children
      strsize = len(data)/size
      for i in xrange(1,size):
        data_block = data[strsize*i:strsize*(i+1)]
        #print "sending: " + data_block
        comm.send(data_block, dest = i)
      data_block = data[0:strsize]
    else:
      # all processes will receive data
      received = comm.recv(source=0)
      #print "received: " + received
      data_block = received
      #print str(rank) + "is receiving:"
      #print data_block

    #print "pre seq"
    #print rank
    #print data_block
    # run sequitur here
    rules = run_sequitur(data_block)
    #print "post seq"
    #print rank
    #print rules

    # need to send all data back to rank 0
    if rank != 0:
      comm.send(rules, dest=0)
    else:
      # rank 0 should append its own data
      cummulative_ruleset.append(rules)
      # rank 0 should receive all data
      for i in xrange(1, size):
        received = comm.recv(source=i)
        print received
        cummulative_ruleset.append(received)

    return cummulative_ruleset

def recombine(list_of_grammars):
    #final_ruleset = {}
    #for grammar in list_of_grammars:
      

def main():
    
    comm = MPI.COMM_WORLD
    rank = comm.Get_rank()

    if rank == 0:
      # process 0 gets all the data first
      data = input_string # need to change this to open an input file
    else:
      # other processes don't get anything until rank 0 sends it
      data = None

    # comm barrier to wait for all results to be completed calculated
    comm.barrier()
    # run the parallel_sequitur driver
    cummulative_ruleset = parallel_sequitur(data,comm)
    comm.barrier()
    
    print "out of barrier..."

    # recombine all the rules
    if rank == 0:
      print cummulative_ruleset
      final_ruleset = {}
      numrules = 0
      #for set in cummulative_ruleset:
        

if __name__ == "__main__":
    main()
