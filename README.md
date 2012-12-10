# Parallel Sequitur #

## Computer Science 205 Fall 2012

### Project Members: ###
* Saagar Deshpande (sdeshpande@college, saagar@hcs.harvard.edu)
* Irene Chen (iychen@college)

### Introduction: ###

We implemented serial and parallel versions of the Sequitur compression algorithm. The Sequitur algorithm uses hierarchical structure and sequences of discrete symbols to compress files by exploiting repetative structures found in strings.

Our final project is based on the algorithm as described in the following paper: "Identifying Hierarchical Structure in Sequences: A linear-time algorithm", Craig G. Nevill-Manning, Ian H. Witten, Journal of Artificial Intelligence Research 7 (1997) 67.82

### Dependencies ###

* MPI (mpi4py)
* Python (sys, csv)

The above packages should already be installed on the resonance.seas cluster for CS 205. On the resonance node, `module load courses/cs205/2012` will load these modules into the user environment automatically.

### Source Code: ###

[Project on Github](https://github.com/raysaagar/parallel-sequitur)

* `serial.py`: serial implementation of the Sequitur algorithm
* `concat_parallel.py`: simple parallel implementation of the Sequitur algorithm. Text is split among workers which run a version of the serial algorithm before the main process merges all results in order.
* `wordcount.py`: parallel implementation based on the Sequitur concept. This method uses frequency of words in the provided text to determine rules to create before compressing.

The repository contains several test cases as well as runscripts to test the code.

### Usage: ###

Run `serial.py` to see the results of the serial implementation.

Run `qsub runscript` to run the `concat_parallel.py` implementation.

Run `qsub wcscript` to run the `wordcount.py` implementation.

### Sources: ###
* Original Paper: [Identifying Hierarchical Structure in Sequences: A linear-time algorithm](http://www.jair.org/media/374/live-374-1630-jair.pdf)
* [Sequitur website](http://sequitur.info/)

### Acknowledgements: ###
* [Bjoern Andres](http://www.andres.sc/) - project mentor
* [Cris Cecka](http://crisco.seas.harvard.edu/), [Hanspeter Pfister](http://gvi.seas.harvard.edu/pfister) - for instructional material on MPI
* [Michael Mitzenmacher](http://www.eecs.harvard.edu/~michaelm/) - for introducing us to Sequitur and a general love of algorithms
* [Craig G. Nevill-Manning](http://craig.nevill-manning.com/), [Ian H. Witten](http://www.cs.waikato.ac.nz/~ihw/) - for creating Sequitur
