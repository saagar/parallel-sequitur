import symbol as Symbol

class Guard(Symbol):
  """
  guard class for sequitur
  based on java implementation
  """

  rule r

  def __guard(self, rule):
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
