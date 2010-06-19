#!/usr/bin/env php
<?

require_once('spreader_helpers.php');
require_once('spreader1.php');
require_once('spreader2.php');

function make_jobs() {
	return new PHP2PyIterator(array(
		new PHP2PyIterator(array("a1", "a2", "a3")),
		new PHP2PyIterator(array("b1")),
		new PHP2PyIterator(array("c1", "c2", "c3"))
	));
}

function quoter($s) {
	return "'$s'";
}

$expected = array(
	1 => array('a1', 'a2', 'a3', 'b1', 'c1', 'c2', 'c3'),
	2 => array('a1', 'b1', 'a2', 'c1', 'a3', 'c2', 'c3'),
	3 => array('a1', 'b1', 'c1', 'a2', 'c2', 'a3', 'c3'),
	4 => array('a1', 'b1', 'c1', 'a2', 'c2', 'a3', 'c3')
);

foreach (array('Spreader1', 'Spreader2') as $implementation) {
	$runs = array();
	for ($spread = 1; $spread <= 4; $spread++) {
		$runs[$spread] = new Py2PHPIterator(new Spreader(make_jobs(), $spread));
	}

	foreach ($runs as $spread => $results) {
		$jobs_out = iterator2array($results);
		if ($jobs_out != $expected[$spread]) {
			print "Mismatch on $implementation $spread\n";
		}
	}
}

foreach ($expected as $jobs_out) {
	print "[".join(", ", array_map('quoter', $jobs_out))."]\n";
}
