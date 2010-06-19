#!/usr/bin/env python

from itertools import cycle

def remove_and_rotate(l, item):
    """Remove the given item from the list, and return a new list that starts at the next position.
    
    >>> remove_and_rotate([1,2,3], 1)
    [2, 3]
    
    >>> remove_and_rotate([1,2,3], 2)
    [3, 1]
    
    >>> remove_and_rotate([1,2,3], 3)
    [1, 2]
    """
    offset = l.index(item)
    return l[offset+1:] + l[:offset]

def spreader_generator(blockpool, spread):
    """Returns an iter over an iter of iters, where the inner elements are interleaved at spread intervals.
    
    The tail has no such guarantees, as we're an eager bin packer.
    
    >>> jobs = [["a1", "a2", "a3"], ["b1"], ["c1", "c2", "c3"]]
    
    >>> list(spreader_generator(jobs, 1))
    ['a1', 'a2', 'a3', 'b1', 'c1', 'c2', 'c3']
    
    >>> list(spreader_generator(jobs, 2))
    ['a1', 'b1', 'a2', 'c1', 'a3', 'c2', 'c3']
    
    >>> list(spreader_generator(jobs, 3))
    ['a1', 'b1', 'c1', 'a2', 'c2', 'a3', 'c3']
    
    >>> list(spreader_generator(jobs, 4))
    ['a1', 'b1', 'c1', 'a2', 'c2', 'a3', 'c3']
    """
    blockpool_iter = iter(blockpool)
    feeders = [feeder_generator(blockpool_iter) for _ in range(spread)]
    feeders_iter = cycle(feeders)
    while len(feeders):
        # Get the next feeder
        feeder = feeders_iter.next()
        try:
            # Return its next value
            yield feeder.next()
        except StopIteration:
            # That feeder is now empty, so remove it
            # and continue where we left off
            feeders = remove_and_rotate(feeders, feeder)
            feeders_iter = cycle(feeders)

def feeder_generator(blockpool):
 for block in blockpool:
     for v in block:
         yield v
