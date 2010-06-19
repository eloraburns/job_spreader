#!/usr/bin/env python

from itertools import chain, ifilter, izip_longest

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
    # This sentinel object is unique to this function and can't equal anything
    # a user can put into the lists, so it's a safe "nothing" value for filler.
    sentinel = object()
    # We need a real iterator for our feeders to share
    blockpool_iter = iter(blockpool)
    feeders = [chain.from_iterable(blockpool_iter) for _ in range(spread)]
    not_sentinel = lambda x: x is not sentinel
    flattened_spread = chain.from_iterable(izip_longest(*feeders, fillvalue=sentinel))
    return ifilter(not_sentinel, flattened_spread)
