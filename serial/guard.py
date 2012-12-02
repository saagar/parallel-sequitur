#from rule import Rule
from terminal import Terminal
from symbol import Symbol
from nonTerminal import NonTerminal

class Guard(Symbol):
  """
  guard class for sequitur
  based on java implementation
  """

  # rule
  r = None

  def __init__(self, rule):
    super(Guard,self).__init__()
    self.r = rule;
    value = 0;
    p = self
    n = self

  def cleanUp(self):
    join(p,n)

  def isGuard(self):
    return True;

  def deleteDigram(self):
    pass

  def check(self):
    return false
