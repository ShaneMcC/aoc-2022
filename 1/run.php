#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	$part1 = 0;
	foreach ($input as $id => $elf) {
		$count = array_sum($elf);
		if ($count > $part1) { $part1 = $count; }
	}

	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
