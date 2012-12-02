#from guard import Guard
#from rule import Rule
#from nonTerminal import NonTerminal
from symbol import Symbol

class Terminal(Symbol):
    """
    terminal class for sequitur
    based on java implementation
    """

    value = None

    def __init__(self, theValue):
        super(Terminal,self).__init__()
        self.value = theValue
        self.p = None
        self.n = None

    def cleanUp(self):
        self.join(self.p,self.n)
        self.deleteDigram()
