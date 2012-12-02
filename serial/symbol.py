import guard as Guard

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
    if(left.n != None):
      left.deleteDigram()
    left.n = right
    right.p = left

  def insertAfter(self,symToInsert):
    join(symToInsert,n)
    join(self,symToInsert)

  def cleanUp(self):
    pass

  # might be fixed - should check this
  def deleteDigram(self):
    if(self.n.isGuard()):
      return
    
    dummy = theDigrams.get(self, None)
    if(dummy == self):
      theDigrams.pop(self)

  def isGuard(self):
    return False

  def isNonTerminal(self):
    return False

  # might be fixed? may want to use get
  def check(self):
    found = None
    if(self.n.isGuard()):
      return False
    if(not self in self.theDigrams):
      found = self.theDigrams[self] = self
      return False
    found = self.theDigrams[self]
    if found.n != selfi:
      match(self, found)
    return True

  # should check this
  def substitute(self, rule):
    cleanUp
    self.n.cleanUp
    r = nonTerminal(rule)
    self.p.insertAfter(r)
    if(not self.p.check()):
      self.p.n.check()

  # should check this
  def match(self, newDigram, matchingSymbol):
    r, first, second, dummy = None, None, None, None
    if matchingSymbol.p.isGuard() and matchingSymbol.n.n.isGuard():
      # reuse existing rule
      r = ((guard)matchingSymbol.p).r
      newDigram.substitute(r)
    else:
      # create a new rule
      r = rule()
      try:
        first = (symbol)newDigram.clone()
        second = (symbol)newDigram.n.clone()
        r.theGuard.n = first
        first.p = r.theGuard
        first.n = second
        second.p = first
        second.n = r.TheGuard
        r.theGuard.p = second

        dummy = (symbol)theDigrams[first] = first
        matchingSymbol.substitute(r)
        newDigram.substitute(r)
      except:
        print "Something broke in match: ", sys.exc_info()[0]
        raise

    # check for underused rule
    if r.first().isNonTerminal() and (nonTerminal)r.first()).r.count == 1:
      ((nonTerminal)r.first()).expand()

  def hashCode(self)
    code = ((21599*self.value) + (20507*self.n.value))
    code = code % self.prime
    return code

  # Test if two digrams are equal
  # Do NOT use to compare two symbols
  def equals(obj):
    return ((self.value == ((symbol)obj).value) and (self.n.value == ((symbol)obj).n.value))
