<?

/**
 * Takes a list of lists, and provides a Python-style iterable over the lists
 * such that elements from each sub-list occur at $spread intervals (until
 * we get to the tail, at which point we have no guarantees; we're an eager
 * bin packer).
 */
class Spreader3 {
	/**
	 * @param $blockpool PHP array of arrays
	 * @param $spread How far apart to spread the values within each sub-list
	 */
	function __construct($blockpool, $spread) {
		reset($blockpool);
		$this->blockpool = $blockpool;
		$this->spread = min(count($blockpool), $spread);
		$this->feeders = array();
		foreach (range(1, $this->spread) as $_) {
			list($__, $feeder) = each($this->blockpool);
			reset($feeder);
			$this->feeders[] = $feeder;
		}
		$this->current_index = 0;
	}
	function next() {
		$found = FALSE;
		do {
			if ($this->spread < 1) {
				# We've got nothing to do
				throw new StopIteration();
			}
			list($key, $next) = each($this->feeders[$this->current_index]);
			if ($key !== NULL) {
				$found = TRUE;
			} else {
				# This block is empty, refill it
				list($key, $this->feeders[$this->current_index]) = each($this->blockpool);
				if ($key === NULL) {
					# The blockpool is empty, so remove this empty feeder
					array_splice($this->feeders, $this->current_index, 1);
					# Our spread is now smaller
					$this->spread--;
					if ($this->spread > 0) {
						# Make sure we don't take a long walk off a short array
						$this->current_index %= $this->spread;
					}
					# And try again from the (previously next, now same) place
					continue;
				}
				# Now try again at the same place
				continue;
			}
			$this->current_index = ($this->current_index + 1) % $this->spread;
		} while (!$found);
		return $next;
	}
}
