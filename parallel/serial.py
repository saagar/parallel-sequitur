import sys
import collections
import re
import csv
import time

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
            # print "deleted rule" + rule
            return False, rules_so_far, rule_list
    return True, rules_so_far, rule_list

def make_test():
    rules = {}
    rules['0'] = '22@222@d'
    rules['1'] = 'aa'
    rules['2'] = '1b'
    return rules

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
            
def main():
    num_rules = 0
    rules = {}
    rules['0'] = ''
    digram_bool = False
    rule_bool = False
    digram_index = []
    unused_rules = []
    
    rules_so_far={}
    rules_so_far['0'] = '' # '@13@abd@13@akjkdf@13@a'
    if len(sys.argv) != 2:
        print "Usage: serial.py filename.txt"
        return 1
    
    input_file = sys.argv[1] 
    output_file = (input_file.split('.')[0])+'_grammar.csv'

    f = open('books/'+input_file, 'rb')
    # g = open('books/CanterburyTales_gramar.txt', 'wb')
    # input_string = "abracadabraarbadacarba"
    input_string = f.read()
    start = time.time()
    for i in range(len(input_string)):
        rules_so_far['0'] = rules_so_far['0'] + input_string[i]
        penult_symbol, last_symbol = last_two_symbols(rules_so_far['0'])
        last_digram = penult_symbol + last_symbol
        # determine if new digram is repeated elsewhere and repetitions do not overlap
        string_occurrences = last_digram in rules_so_far['0'][:-1] #all_occurrences(last_digram, rules_so_far['0'][:-1])
        # other_occurrences = total_occurrences[:-1]
        # last_range = total_occurrences[-1]
        # string_occurrences = non_overlap(other_occurrences, last_range)
        all_rules = [a for a in rules_so_far.keys() if  '0' != a]
        
        # build rule_occurrences
        rule_occurrences = []
        for x in all_rules:
            if last_digram in rules_so_far[x]:
                if rules_so_far[x] == last_digram:
                    rule_occurrences.append((x,True))
                else:
                    rule_occurrences.append((x,False))
                # print "last_digram: " + last_digram
                # print digram_index
                # print "error"

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
                        # print "added"+str(num_rules)
                    else:
                        # otherwise, reuse one of the unused rule numbers
                        new_num = unused_rules.pop(0)
                        rules_so_far[new_num] = last_digram
                        try:
                            rules_so_far[rule_num] = rules_so_far[rule_num].replace(last_digram, '@'+new_num+'@')
                        except:
                            for x in [a for a in rules_so_far.keys() if  '0' != a]:
                                rules_so_far[x] = rules_so_far[x].replace(last_digram, '@'+new_num+'@')
                        # print "added"+new_num
                    rule_utility_bool = False
                    while not rule_utility_bool:
                        rule_utility_bool, rules_so_far, unused_rules = rule_utility(rules_so_far, unused_rules)      
            # if digram repeats in string, create new rule and enforce
            if string_occurrences:
                if unused_rules == []:
                    num_rules += 1
                    rules_so_far[str(num_rules)] = last_digram
                    rules_so_far['0'] = rules_so_far['0'].replace(last_digram, '@'+str(num_rules)+'@')
                    # print "added"+str(num_rules)
                else:
                    new_num = unused_rules.pop(0)
                    rules_so_far[str(new_num)] = last_digram
                    rules_so_far['0'] = rules_so_far['0'].replace(last_digram, '@'+new_num+'@')
                    # print "added"+new_num
                rule_utility_bool = False
                while not rule_utility_bool:
                    rule_utility_bool, rules_so_far, unused_rules = rule_utility(rules_so_far, unused_rules)
    end = time.time()
    # print rules_so_far
    writer = csv.writer(open(output_file, 'wb'))
    for key, value in rules_so_far.items():
        writer.writerow([key, value])
    #    g.write(rules_so_far)
    writer = csv.writer(open('mainstring.txt', 'wb'))
    writer.writerow(rules_so_far['0'])
    
    print "time: %f s" % (end - start)
    
if __name__ == "__main__":
    main()
