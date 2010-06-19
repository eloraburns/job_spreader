#!/usr/bin/env python

import doctest

# Example workload
jobs = [["a1", "a2", "a3"], ["b1"], ["c1", "c2", "c3"]]
# The order we expect to find the jobs given a spread
outputs = {
    1: ['a1', 'a2', 'a3', 'b1', 'c1', 'c2', 'c3'],
    2: ['a1', 'b1', 'a2', 'c1', 'a3', 'c2', 'c3'],
    3: ['a1', 'b1', 'c1', 'a2', 'c2', 'a3', 'c3'],
    4: ['a1', 'b1', 'c1', 'a2', 'c2', 'a3', 'c3']
}

# Test the class-iterator-based version
from spreader1 import Spreader
for n, expected in outputs.iteritems():
    assert list(Spreader(jobs, n)) == expected, ('spreader1', n)

# Test all the generator-based versions
generator_type_spreaders = [
    'spreader2', 'spreader2_documented',
    'spreader3', 'spreader3_documented',
    'spreader4', 'spreader4_documented', 'spreader4_short',
]

for modname in generator_type_spreaders:
    mod = __import__(modname)
    doctest.testmod(m=mod)
    for n, expected in outputs.iteritems():
        assert list(mod.spreader_generator(jobs, n)) == expected, (modname, n)

# Just to show that we're verifying against the same thing as the PHP
for n in sorted(outputs.iterkeys()):
    print outputs[n]
