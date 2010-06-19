class Spreader(object):
    def __init__(self, blockpool, spread):
        blockpool_iter = iter(blockpool)
        self.feeders = [Feeder(blockpool_iter) for _ in range(spread)]
        self.current = 0
    def __iter__(self):
        return self
    def next(self):
        next = None
        while next is None:
            try:
                next = self.feeders[self.current].next()
            except StopIteration:
                del self.feeders[self.current]
                if len(self.feeders) <= 0:
                    raise StopIteration
                self.current %= len(self.feeders)
                continue
            self.current = (self.current + 1) % len(self.feeders)
        return next

class Feeder(object):
    def __init__(self, blockpool):
        self.blockpool = blockpool
        self.block = iter([])
    def next(self):
        while True:
            try:
                return self.block.next()
            except StopIteration:
                self.block = iter(self.blockpool.next())
