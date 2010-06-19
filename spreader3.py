from itertools import cycle

def remove_and_rotate(l, item):
    offset = l.index(item)
    return l[offset+1:] + l[:offset]

def spreader_generator(blockpool, spread):
    blockpool_iter = iter(blockpool)
    feeders = [feeder_generator(blockpool_iter) for _ in range(spread)]
    feeders_iter = cycle(feeders)
    while len(feeders):
        feeder = feeders_iter.next()
        try:
            yield feeder.next()
        except StopIteration:
            feeders = remove_and_rotate(feeders, feeder)
            feeders_iter = cycle(feeders)

def feeder_generator(blockpool):
 for block in blockpool:
     for v in block:
         yield v
