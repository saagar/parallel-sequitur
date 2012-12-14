import sys
import collections
import re
import csv
import time
from mpi4py import MPI
import numpy as np
import math

# helper function to enforce rule utility
def rule_utility(rules_so_far, rule_list):

    for rule in [a for a in rules_so_far.keys() if a <> '0']:
        # count how many times each rule besides the main string is used
        count = 0
        other_keys = [a for a in rules_so_far.keys() if rule <> a]
        for key in other_keys:
            count += rules_so_far[key].count(rule)

        # if rule is only used once, consolidate
        if count < 2:
            for key in other_keys:
                rules_so_far[key] = rules_so_far[key].replace('@'+rule+'@', rules_so_far[rule])
            del rules_so_far[rule]
            rule_list.append(rule)

            # return false flag to ensure that all rules have been properly caught
            return False, rules_so_far, rule_list
        
    # after one clean time through, done!
    return True, rules_so_far, rule_list

# helper function to find last symbol
def last_symbol(test_string):
    # if a letter, return letter
    if input_str[-1] != '@':
        result = input_str[-1]
    else:
        # find the other end of rule
        place_holder = -2
        while input_str[place_holder] != '@':
            place_holder -= 1
        result = input_str[place_holder:]
    return result

# helper function to find first symbol
def first_symbol(test_string):
    # if a letter, return letter
    if input_str[0] != '@':
        result = input_str[0]
    else:
        # find the other end of rule
        place_holder = 1
        while input_str[place_holder] != '@':
            place_holder += 1
        result = input_str[place_holder:]
    return result

def digram_uniqueness(rules, num_rules):
    # find target digram
    last_digram = ''.join(last_two_symbols(rules['0']))

    count = 0
    # count how many times it appears in the grammar beforehand
    for key in rules.keys():
        count += rules[key].count(last_digram)

    if count == 1:
        # if exactly once, done!
        return True, rules, num_rules

    else:
        # otherwise, create a new rule for this digram
        num_rules += 1
        for x in rules.keys():
            rules[x] = rules[x].replace(last_digram, str(num_rules))
            rules[str(num_rules)] = last_digram
        return False, rules, num_rules

def merge_and_replace(list_of_grammars):
    masterset = {}
    back_dict = {}

    num_rules = 0

    # re-number all rules for all grammars in list of grammars
    for grammar in list_of_grammars:
        
        all_rules = [a for a in grammar.keys() if a != '0']
        
        # for each rule, if the actual rule hasn't been seen before
        for x in all_rules:
            # create new rule and insert into record
            if not grammar[x] in back_dict.keys():
                num_rules += 1
                # re-number rule and add to list of rules that have been seen before
                grammar['0'] = grammar['0'].replace('@'+x+'@', '@'+str(num_rules)+'@')
                back_dict[grammar[x]] = str(num_rules)
                masterset[str(num_rules)] = grammar[x]

            else:
                try:
                    # if the rule has been seen before
                    rule_to_use = back_dict[masterset[x]]
                    # use previous rule to rewrite main string
                    grammar['0'].replace(x, str(rule_to_use))
                    # save accordingly
                    grammar[rule_to_use] = grammar[x]
                except:
                    num_rules += 1
                    # re-number rule and add to list of rules that have been seen before
                    grammar['0'] = grammar['0'].replace('@'+x+'@', '@'+str(num_rules)+'@')
                    back_dict[grammar[x]] = str(num_rules)
                    masterset[str(num_rules)] = grammar[x]

                                                                                                 
    # find all rules '0' and merge them
    stringMaster = ''
    for grammar in list_of_grammars:
        stringMaster = stringMaster + grammar['0']
    
    # push rule '0' into masterset
    masterset['0'] = stringMaster

    # done! pat yourself on the back and return masterset
    return masterset, num_rules

# testing function
def make_test():
    rule1 = {}
    rule1['0'] = '@1@@3@@2@@1@@2@@3@'
    rule1['1'] = 'aa'
    rule1['2'] = 'ab'
    rule1['3'] = 'cb'

    rule2 = {}
    rule2['0'] = '@3@@1@@2@@2@@1@@3@'
    rule2['1'] = 'ac'
    rule2['2'] = 'aa'
    rule2['3'] = 'ba'
    return [rule1, rule2]

def last_two_symbols(input_str):
    # if the input string is less than 2 characters long, just output
    if len(input_str) < 2:
        return '', input_str

    # make sure last symbol isn't a rule
    place_holder = -1
    if input_str[-1] != '@':
        last_symbol = input_str[-1]
    else:
        # if it is, find the other end of rule
        place_holder = -2
        while input_str[place_holder] != '@':
            place_holder -= 1
        last_symbol = input_str[place_holder:]
    # repeat for second to last symbol
    place_holder2 = place_holder - 1
    if input_str[place_holder2] != '@':
        penultimate_symbol = input_str[place_holder2:place_holder]
    else:
        place_holder2 -= 1
        while input_str[place_holder2] != '@':
            place_holder2 -= 1
        penultimate_symbol = input_str[place_holder2:place_holder]
    return penultimate_symbol, last_symbol

def all_occurrences(digram, search_string):
    return [(a.start(), a.end()) for a in list(re.finditer(digram, search_string))]

def non_overlap(list_of_ranges, target_range):
    t1, t2 = target_range
    overlapping_ranges = []
    for x in list_of_ranges:
        a,b = x
        if not (list(set(xrange(a,b)) & set(xrange(t1,t2))) != []):
            overlapping_ranges.append(x)
    return overlapping_ranges

# very similar to serial implementation in serial.py
def run_sequitur(input_string, comm):

    rank = comm.Get_rank()

    # initialize values
    num_rules = 0
    unused_rules = []
    rules_so_far={}
    rules_so_far['0'] = ''

    # serially iterate over all letters in string
    for i in range(len(input_string)):

        rules_so_far['0'] = rules_so_far['0'] + input_string[i]
        penult_symbol, last_symbol = last_two_symbols(rules_so_far['0'])
        last_digram = penult_symbol + last_symbol
        # determine if new digram is repeated elsewhere and repetitions do not overlap
        string_occurrences = last_digram in rules_so_far['0'][:-1] 
        all_rules = [a for a in rules_so_far.keys() if  '0' != a]
        
        # build rule_occurrences
        rule_occurrences = []
        for x in all_rules:
            if last_digram in rules_so_far[x]:
                if rules_so_far[x] == last_digram:
                    rule_occurrences.append((x,True))
                else:
                    rule_occurrences.append((x,False))

        # if the new digram is repeated elsewhere and nonoverlapping
        if (rule_occurrences != []) or (string_occurrences):
            for x in rule_occurrences:
                rule_num, entire_bool = x
                # if this occurrence is a complete rule, enforce rule
                if entire_bool:
                    # replace new digram with corresponding rule
                    rules_so_far['0'] = rules_so_far['0'].replace(last_digram, '@'+rule_num+'@')
                    rule_utility_bool = False
                    while not rule_utility_bool:
                        rule_utility_bool, rules_so_far, unused_rules = rule_utility(rules_so_far, unused_rules)
                else:
                    # otherwise create new rule and replace inside other rule
                    if unused_rules == []:
                        # if there are no unused rules, increment num_rules
                        num_rules += 1
                        rules_so_far[str(num_rules)] = last_digram
                        rules_so_far[rule_num] = rules_so_far[rule_num].replace(last_digram, '@'+str(num_rules)+'@')

                    else:
                        # otherwise, reuse one of the unused rule numbers
                        new_num = unused_rules.pop(0)
                        rules_so_far[new_num] = last_digram
                        try:
                            rules_so_far[rule_num] = rules_so_far[rule_num].replace(last_digram, '@'+new_num+'@')
                        except:
                            for x in [a for a in rules_so_far.keys() if  '0' != a]:
                                rules_so_far[x] = rules_so_far[x].replace(last_digram, '@'+new_num+'@')
                    rule_utility_bool = False
                    while not rule_utility_bool:
                        rule_utility_bool, rules_so_far, unused_rules = rule_utility(rules_so_far, unused_rules)      
            # if digram repeats in string, create new rule and enforce
            if string_occurrences:
                if unused_rules == []:
                    num_rules += 1
                    rules_so_far[str(num_rules)] = last_digram
                    rules_so_far['0'] = rules_so_far['0'].replace(last_digram, '@'+str(num_rules)+'@')

                else:
                    new_num = unused_rules.pop(0)
                    rules_so_far[str(new_num)] = last_digram
                    rules_so_far['0'] = rules_so_far['0'].replace(last_digram, '@'+new_num+'@')

                rule_utility_bool = False
                while not rule_utility_bool:
                    rule_utility_bool, rules_so_far, unused_rules = rule_utility(rules_so_far, unused_rules)
    return rules_so_far
    
def parallel_sequitur(data, comm):
    rank = comm.Get_rank()
    size = comm.Get_size()

    # store rules for each process and total
    rules = {}
    cummulative_ruleset = []
    
    if rank == 0:
        strsize = len(data)/size
        for i in xrange(1,size):
            data_block= data[strsize*i:strsize*(i+1)]
            comm.send(data_block, dest=i)
        data_block = data[0:strsize]
    else:
        received = comm.recv(source=0)
        data_block = received

    rules = run_sequitur(data_block, comm)

    if rank != 0:
        comm.send(rules, dest=0)
    else:
        cummulative_ruleset.append(rules)
        for i in xrange(1,size):
            received = comm.recv(source=i)
            cummulative_ruleset.append(received)
    return cummulative_ruleset

def main():
    start = time.time()
    # correct usage if necessary
    if len(sys.argv) != 2:
        if rank == 0:
            print "Usage: concat_parallel.py filename.txt"
        return 1
    
    # set up MPI
    comm = MPI.COMM_WORLD
    rank = comm.Get_rank()
    size = comm.Get_size()

    # set up file names
    input_file = sys.argv[1]
    output_file = input_file.split('.')[0]+'_grammar.csv'
    mainstring_file = input_file.split('.')[0]+'_mainstring.txt'

    if rank == 0:
        # process 0 gets all data first
        input_file = sys.argv[1]
        f = open('books/'+input_file, 'rb')
        input_string = f.read()
        data = input_string
    else:
        # all other processes don't get anything until rank 0 sends them it
        data = None
        
    # comm barrier to wait for all results to be sent
    comm.barrier()

    # run the parallel_sequitur driver
    cummulative_ruleset = parallel_sequitur(data,comm)
    comm.barrier()

        
    # recombine all the rules
    if rank == 0:
        unused_rules = []
        masterrules, num_rules = merge_and_replace(cummulative_ruleset)
        digram_bool, rule_bool = False, False
        while not digram_bool or not rule_bool:
            digram_bool, masterrules, num_rules = digram_uniqueness(masterrules, num_rules)
            rule_bool, masterrules, unused_rules = rule_utility(masterrules, unused_rules)            

        output_file = input_file.split('.')[0]+'_grammar.csv'
        writer = csv.writer(open('results/'+output_file, 'wb'))
        for key, value in masterrules.items():
            writer.writerow([key, value])
        output_string = input_file.split('.')[0]+'_mainstring.txt'
        writer2 = open('results/'+output_string, 'wb')
        writer2.write(masterrules['0'])
    end = time.time()
    if rank == 0:
        print "time: %f s" % (end-start)
    return 0

def testing():
    testlist = make_test()
    result = merge_and_replace(testlist)
    unused_rules = []
    digram_bool, rule_bool = False, False
    while not digram_bool or not rule_bool:
        digram_bool, testmaster = digram_uniqueness(result)
        rule_bool, testmaster, unused_rules = rule_utility(result, unused_rules)
    print result

if __name__ == "__main__":
    main()
