import sys
import collections

input_string = "abracadabraarbadacarba"
num_rules = 0

def digram_utility(rules_so_far):
    # find last digram created
    last_digram = rules_so_far['0'][-2:]
    count = 0
    # count how many times it appears in the grammar beforehand
    for key in rules_so_far.keys():
        count += rules_so_far[key].count(last_digram)
    print count
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

def main():
    rules = {}
    rules['0'] = ''
    digram_bool = False
    rule_bool = False

    for x in range(len(input_string)):
        digram_bool = False
        rule_bool = False
        string_so_far = input_string[:x]
        rules['0'] = rules['0'] + input_string[x]
        while not digram_bool:
            digram_bool, rules = digram_utility(rules)
        while not rule_bool:
            rule_bool, rules = rule_utility(rules)
    print rules

if __name__ == "__main__":
    main()
