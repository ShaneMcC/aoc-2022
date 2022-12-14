#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	$all = array_map('array_sum', $input);
	rsort($all);

	$part1 = $all[0];
	echo 'Part 1: ', $part1, "\n";

	$part2 = $all[0] + $all[1] + $all[2];
	echo 'Part 2: ', $part2, "\n";
