from rule import Rule
from guard import Guard
from terminal import Terminal
from symbol import Symbol
from nonTerminal import NonTerminal

class Sequitur:
    def __init__(self):
        firstRule = Rule()

        i = 0
        # reset number of rules and hashtable
        Rule.numRules = 0
        #symbol.theDigrams.clear()
        # loop until done
        done = False
        input_string = "abracadabraarbadacarba"
        for i in input_string:
            #print "before: " + str(Rule.numRules)
            firstRule.last().insertAfter(Terminal(i))
            #print "middle: " + str(Rule.numRules)
            firstRule.last().p.check()
            #print "after: " + str(Rule.numRules)
        print firstRule.getRules()
        print firstRule.theGuard.n
        print firstRule.theGuard.n.value
        print firstRule.theGuard.p.value


def main():
  s = Sequitur()

if __name__ == "__main__":
  main()

