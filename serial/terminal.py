import symbol as Symbol

class Terminal(Symbol):
    """
    terminal class for sequitur
    based on java implementation
    """

    theValue = None

    def __init__(self, theValue):
        super(Terminal,self).__init__(theValue)
        self.value = theValue
        p = null
        n = null

    def cleanUp(self):
        join(self.p,self.n)
        deleteDigram()
