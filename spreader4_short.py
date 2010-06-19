from itertools import *

def spreader_generator(b, s):
    class n(object): pass
    i = iter(b)
    return ifilter(lambda x: x is not n, chain.from_iterable(izip_longest(*[chain.from_iterable(i) for _ in range(s)], fillvalue=n)))
