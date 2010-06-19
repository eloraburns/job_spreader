#!/usr/bin/env php
<?

require_once('spreader_helpers.php');
require_once('spreader1.php');
require_once('spreader2.php');

$jobs = array(
	array("a1", "a2", "a3"),
	array("b1"),
	array("c1", "c2", "c3")
);

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


$runs = array();
foreach (range(1,4) as $spread) {
	$runs[$spread] = iterator2array(new Py2PHPIterator(new Spreader1(make_jobs(), $spread)));
}

foreach ($runs as $spread => $results) {
	if ($results != $expected[$spread]) {
		print "Mismatch on Spreader1 $spread\n";
		var_dump($expected[$spread], $results);
	}
}


$runs = array();
foreach (range(1,4) as $spread) {
	$runs[$spread] = iterator2array(new Py2PHPIterator(new Spreader2($jobs, $spread)));
}

foreach ($runs as $spread => $results) {
	if ($results != $expected[$spread]) {
		print "Mismatch on Spreader2 $spread\n";
		var_dump($expected[$spread], $results);
	}
}

foreach ($expected as $results) {
	print "[".join(", ", array_map('quoter', $results))."]\n";
}
