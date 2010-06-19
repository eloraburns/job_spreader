<?

/**
 * Takes a list of lists, and provides a Python-style iterable over the lists
 * such that elements from each sub-list occur at $spread intervals (until
 * we get to the tail, at which point we have no guarantees; we're an eager
 * bin packer).
 */
class Spreader1 {
	/**
	 * @param $blockpool Python-style iterable of Python-style iterables
	 * @param $spread How far apart to spread the values within each sub-list
	 */
	function __construct($blockpool, $spread) {
		$this->spread = $spread;
		$this->feeders = array();
		foreach (range(1,$this->spread) as $_) {
			$this->feeders[] = new Feeder1($blockpool);
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
class Feeder1 {
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
