class Symbol:
  
  numTerminals = 100000
  prime = 2265539

  theDigrams = {}
  value = 0
  p, n = None, None
  
  def __init__(self):
    self.p = None
    self.n = None

  def join(self,left, right):
    if(left.n != None)
      left.deleteDigram()
    left.n = right
    right.p = left

  def insertAfter(self,symToInsert):
    join(symToInsert,n)
    join(self,symToInsert)

  def cleanUp(self):
    pass

  # might be fixed
  def deleteDigram(self):
    if(self.n.isGuard()):
      return
    
    dummy = theDigrams.get(self, None)
    if(dummy == self)
      theDigrams.pop(self)

  def isGuard(self):
    return False

  def isNonTerminal(self):
    return False

  # might be fixed? may want to use get
  def check(self):
    found = None
    if(self.n.isGuard())
      return False
    if(not self in self.theDigrams):
      found = self.theDigrams[self] = self
      return False
    found = self.theDigrams[self]
    if found.n != self
      match(self, found)
    return True



