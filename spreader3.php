<?
class Spreader3 {
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
				throw new StopIteration();
			}
			list($key, $next) = each($this->feeders[$this->current_index]);
			if ($key !== NULL) {
				$found = TRUE;
			} else {
				list($key, $this->feeders[$this->current_index]) = each($this->blockpool);
				if ($key === NULL) {
					array_splice($this->feeders, $this->current_index, 1);
					$this->spread--;
					if ($this->spread > 0) {
						$this->current_index %= $this->spread;
					}
					continue;
				}
				continue;
			}
			$this->current_index = ($this->current_index + 1) % $this->spread;
		} while (!$found);
		return $next;
	}
}
