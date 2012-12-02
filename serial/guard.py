from rule import Rule
from terminal import Terminal
import symbol as Symbol
import nonTerminal as Terminal

class Guard(Symbol):
  """
  guard class for sequitur
  based on java implementation
  """

  rule r

  def __init__(self, rule):
    super(Guard,self).__init__(rule)
    self.r = rule;
    value = 0;
    p = self
    n = self

  def cleanUp():
    join(p,n)

  def isGuard():
    return True;

  def deleteDigram:
    pass

  def check:
    return false
