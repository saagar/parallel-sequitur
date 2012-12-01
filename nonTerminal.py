class nonTerminal:
    """
    nonTerminal class for sequitur
    based on java implementation
    """

    rule r

    def __init__(self, theRule):
        r = theRule
        r.count += 1
        value = numTerminals + r.number
        p = null
        n = null

    class clone:
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
