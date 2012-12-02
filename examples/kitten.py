from cat import Cat

class Kitten(Cat):

  def __init__(self,name,age,color):
    super(Kitten,self).__init__(name,age)
    self.type = "kitten"
    self.count += 1
    print "KITTEN IS " + color
