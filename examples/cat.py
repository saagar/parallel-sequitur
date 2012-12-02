class Cat(object):
  count = 0
  name = None
  age = None
  type = None

  def __init__(self, name, age):
    self.name = name
    self.age = age
    self.type = "cat"
    Cat.count += 1

  def meow(self):
    print self.name + " is a " + self.type + " that goes meow!"
