#from guard import Guard
#from rule import Rule
from terminal import Terminal
from symbol import Symbol

class NonTerminal(Symbol):
    """
    nonTerminal class for sequitur
    based on java implementation
    """

    #rule
    r = None

    def __init__(self, theRule):
        r = theRule
        r.count += 1
        value = numTerminals + r.number
        p = null
        n = null

    def clone(self):
        sym = nonTerminal(self.r)
        sym.p = p
        sym.n = n
        return sym

    def cleanUp():
        join(p,n)
        deleteDigram()
        self.r.count -= 1

    def isNonTerminal():
        return true

    """
    this symbol is the last reference to its rule.
    the contents of the rule are substituted in
    its place.
    """

    def expand():
        join(p,r.first())
        join(r.last(),n)

        theDigrams.put(r.last(), r.last())

        r.theGuard.r = null
        r.theGuard = null
