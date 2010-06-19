#!/usr/bin/env php
<?

/**
 * Because Python got it right.
 */
class StopIteration extends Exception {}

/**
 * Converts a PHP-style iterable into a Python-style iterable.
 * ->next() returns values until the iterable is empty, then starts raising StopIteration.
 */
class PHP2PyIterator {
	function __construct($iterator) {
		$this->iterator = $iterator;
	}
	function next() {
		list($key, $value) = each($this->iterator);
		if ($key === NULL) {
			throw new StopIteration();
		}
		return $value;
	}
}

/**
 * Converts a Python-style iterable into a PHP-style iterable.
 * Returns a constant key() and doesn't implement rewind(),
 * since foreach() doesn't need them, and neither do you
 * if you're using each() yourself.  Just make sure you check
 * === NULL for a key, because NULL == 0.
 */
class Py2PHPIterator implements Iterator {
	function __construct($py_iterator) {
		$this->py_iterator = $py_iterator;
		$this->next();
	}
	function current() {
		return $this->current_value;
	}
	function key() {
		return 0;
	}
	function next() {
		try {
			$this->current_value = $this->py_iterator->next();
			$this->valid = TRUE;
		} catch (StopIteration $e) {
			$this->current_value = FALSE;
			$this->valid = FALSE;
		}
	}
	function rewind() {
		/* Do nothing */
	}
	function valid() {
		return $this->valid;
	}
}

/**
 * Takes a list of lists, and provides a Python-style iterable over the lists
 * such that elements from each sub-list occur at $spread intervals (until
 * we get to the tail, at which point we have no guarantees; we're an eager
 * bin packer).
 */
class Spreader {
	/**
	 * @param $blockpool Python-style iterable of Python-style iterables
	 * @param $spread How far apart to spread the values within each sub-list
	 */
	function __construct($blockpool, $spread) {
		$this->spread = $spread;
		$this->feeders = array();
		for ($x = 0; $x < $this->spread; $x++) {
			$this->feeders[] = new Feeder($blockpool);
		}
		$this->current_index = 0;
	}
	function next() {
		$found = FALSE;
		do {
			if ($this->spread < 1) {
				throw new StopIteration();
			}
			try {
				$next = $this->feeders[$this->current_index]->next();
				$found = TRUE;
			} catch (StopIteration $e) {
				array_splice($this->feeders, $this->current_index, 1);
				$this->spread--;
				if ($this->spread > 0) {
					$this->current_index %= $this->spread;
				}
				continue;
			}
			$this->current_index = ($this->current_index + 1) % $this->spread;
		} while (!$found);
		return $next;
	}
}

/**
 * @param $blockpool Must be something implementing a ->next() method, and throws StopIteration when it's empty.  Its values must also do this.
 */
class Feeder {
	function __construct($blockpool) {
		$this->blockpool = $blockpool;
		$this->block = NULL;
	}
	function next() {
		$found = FALSE;
		if ($this->block === NULL) {
			$this->block = $this->blockpool->next();
		}
		do {
			try {
				$next = $this->block->next();
				$found = TRUE;
			} catch (StopIteration $e) {
				$this->block = $this->blockpool->next();
			}
		} while (!$found);
		return $next;
	}
}

function make_jobs() {
	return new PHP2PyIterator(array(
		new PHP2PyIterator(array("a1", "a2", "a3")),
		new PHP2PyIterator(array("b1")),
		new PHP2PyIterator(array("c1", "c2", "c3"))
	));
}

$runs = array();
for ($x = 1; $x <= 4; $x++) {
	$runs[] = new Spreader(make_jobs(), $x);
}

foreach ($runs as $run) {
	$jobs_out = array();
	foreach (new Py2PHPIterator($run) as $job) {
		$jobs_out[] = "'$job'";
	}
	print "[".join(", ", $jobs_out)."]\n";
}
