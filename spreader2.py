def spreader_generator(blockpool, spread):
    blockpool_iter = iter(blockpool)
    feeders = [feeder_generator(blockpool_iter) for _ in range(spread)]
    current = 0
    while True:
        try:
            yield feeders[current].next()
        except StopIteration:
            del feeders[current]
            if len(feeders) <= 0:
                return
            current %= len(feeders)
            continue
        current = (current + 1) % len(feeders)

def feeder_generator(blockpool):
   for block in blockpool:
       for v in block:
           yield v
