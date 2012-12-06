import numpy as np
from mpi4py import MPI

if __name__ == '__main__':
 
  comm = MPI.COMM_WORLD
  rank = comm.Get_rank()
  size = comm.Get_size()

  #special_char = [0]*size
  special_char = str(rank)
  
  #  empty = np.zeros(size, dtype='d')
  chars = []*size

  things = comm.allgather(special_char)

  #comm.Allgather(special_char, chars)

  print str(rank) + ": " + str(things)



