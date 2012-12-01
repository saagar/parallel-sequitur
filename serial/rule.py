class Rule:
  theGuard = guard()
  # counter for number of times rule is used
  count = 0
  # total number of rules
  numRules = 0
  
  # rule number
  number = -1
  # index used for printing
  index = -1

  def __init__(self):
    number = self.numRules
    self.numRules += 1
    theGuard = guard(self)
    count = 0
    index = 0

  def first():
    return theGuard.n

  def last():
    return theGuard.p

  getRules()

