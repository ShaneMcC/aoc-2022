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

	$all = [];
	foreach ($input as $id => $elf) {
		$all[] = array_sum($elf);
	}
	rsort($all);

	$part2 = $all[0] + $all[1] + $all[2];
	echo 'Part 2: ', $part2, "\n";
