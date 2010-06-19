from itertools import *

def spreader_generator(b, s):
    class n(object): pass
    i = iter(b)
    return ifilter(lambda x: x is not n, chain.from_iterable(izip_longest(*[f(i) for _ in range(s)], fillvalue=n)))

def f(b):
    for v in chain.from_iterable(b):
        yield v
