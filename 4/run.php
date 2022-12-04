#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$part1 = 0;
	$entries = [];
	foreach ($input as $line) {
		preg_match('#(.*)-(.*),(.*)-(.*)#SADi', $line, $m);
		[, $s1, $e1, $s2, $e2] = $m;

		$contains1 = ($s1 >= $s2 && $e1 <= $e2);
		$contains2 = ($s2 >= $s1 && $e2 <= $e1);

		if ($contains1 || $contains2) { $part1++; }

	}

	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
