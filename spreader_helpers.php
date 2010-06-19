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
 * Returns a constant key() and doesn't implement rewind();
 * foreach() doesn't need them, and neither do you.
 * If you're using each() just make sure you check
 * key === NULL, because (0 == NULL) == TRUE.
 * Yes that's intentional, it's to make sure you do it right.
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

function iterator2array($iterator) {
	$array = array();
	foreach ($iterator as $value) {
		$array[] = $value;
	}
	return $array;
}
