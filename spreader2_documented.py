def spreader_generator(blockpool, spread):
    # We explicitly call iter(blockpool) so our feeder_generators
    # can share its iterated state.
    blockpool_iter = iter(blockpool)
    feeders = [feeder_generator(blockpool_iter) for _ in range(spread)]
    current = 0
    while True:
        try:
            yield feeders[current].next()
        except StopIteration:
            # Once a feeder can't satisfy us, we remove it
            del feeders[current]
            # If we're out of feeders, we're done
            if len(feeders) <= 0:
                return
            # We still have feeders, but have to make sure we
            # didn't remove the last one
            current %= len(feeders)
            # And try again
            continue
        # We got one from this feeder, so on to the next
        current = (current + 1) % len(feeders)

def feeder_generator(blockpool):
    """Returns all elements of the lists of blockpool"""
    for block in blockpool:
        for v in block:
            yield v
