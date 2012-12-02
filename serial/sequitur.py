import rule as Rule

class Sequitur:
    def __init__(self):
        firstRule = Rule()
        i = 0
        // reset number of rules and hashtable
        rule.numRules = 0
        symbol.theDigrams.clear()
        // loop until done
        done = False
        input_string = "abracadabraarbadacarba"
        for i in input_string:
            firstRule.last().insertAfter(Terminal(i))
            firstRule.last().p.check()
        print firstRule.getRules()
            
