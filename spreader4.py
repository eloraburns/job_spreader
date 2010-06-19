from itertools import chain, ifilter, izip_longest

def spreader_generator(blockpool, spread):
    sentinel = object()
    blockpool_iter = iter(blockpool)
    feeders = [chain.from_iterable(blockpool_iter) for _ in range(spread)]
    flattened_spread = chain.from_iterable(izip_longest(*feeders, fillvalue=sentinel))
    not_sentinel = lambda x: x is not sentinel
    return ifilter(not_sentinel, flattened_spread)
