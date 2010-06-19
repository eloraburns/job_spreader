#!/usr/bin/env php
<?

require_once('spreader.php');

function make_jobs() {
	return new PHP2PyIterator(array(
		new PHP2PyIterator(array("a1", "a2", "a3")),
		new PHP2PyIterator(array("b1")),
		new PHP2PyIterator(array("c1", "c2", "c3"))
	));
}

$expected = array(
	1 => array('a1', 'a2', 'a3', 'b1', 'c1', 'c2', 'c3'),
	2 => array('a1', 'b1', 'a2', 'c1', 'a3', 'c2', 'c3'),
	3 => array('a1', 'b1', 'c1', 'a2', 'c2', 'a3', 'c3'),
	4 => array('a1', 'b1', 'c1', 'a2', 'c2', 'a3', 'c3')
);

$runs = array();
for ($spread = 1; $spread <= 4; $spread++) {
	$runs[$spread] = new Spreader(make_jobs(), $spread);
}

function quoter($s) {
	return "'$s'";
}

foreach ($runs as $spread => $run) {
	$jobs_out = array();
	foreach (new Py2PHPIterator($run) as $job) {
		$jobs_out[] = $job;
	}
	if ($jobs_out != $expected[$spread]) {
		print "Mismatch on $spread\n";
	}
	print "[".join(", ", array_map('quoter', $jobs_out))."]\n";
}
