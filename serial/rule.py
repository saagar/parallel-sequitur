from guard import Guard
from nonTerminal import NonTerminal
from terminal import Terminal
from symbol import Symbol

class Rule(object):
  # guard symbol to mark beginning and end of rule.
  theGuard = None
  # counter for number of times rule is used
  count = 0
  # total number of rules, this should always be static
  numRules = 0
  
  # rule number
  number = None
  # index used for printing
  index = None

  def __init__(self):
    self.number = Rule.numRules
    Rule.numRules += 1
    self.count = 0
    self.index = 0
    self.theGuard = Guard(self)

  def first(self):
    return self.theGuard.n

  def last(self):
    return self.theGuard.p

  def getRules(self):
    rules = []
    currentRule, referedToRule = None, None
    sym = None
    index = 0
    processedRules = 0
    text = ""
    charCounter = 0

    text = text + "Usage\tRule\n"
    rules.append(self)
    while (processedRules < Rule.numRules):
      currentRule = rules[processedRules]#(rule)rules[processedRules]
      text = text + " " + currentRule.count + "\tR" + processedRules + " -> "
      sym = currentRule.first()
      # modified original for loop; need to do rule tracing with while loop
      while((sym is not None) or (not sym.isGuard())):
        if(sym.isNonTerminal()):
          referedToRule = sym.r#((nonTerminal)sym).r
          if ((Rule.numRules > referedToRule.index) and (rules[referedToRule.index] == referedToRule)):
            index = referedToRule.index
          else:
            index = Rule.numRules
            referedToRule.index = index
            rules.append(referedToRule)
          text = text + "R" + index
        else:
          if(sym.value == " "):
            text = text + "_"
          else:
            if sym.value == "\n":
              text = text + "\\n"
            else:
              text = text + sym.value
        text = text + " "
        sym = sym.n
      text = text + "\n"
      processedRules += 1
    return text
