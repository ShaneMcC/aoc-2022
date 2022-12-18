#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$map = [];
	$cubes = [];
	foreach ($input as $line) {
		preg_match('#(.*),(.*),(.*)#SADi', $line, $m);
		[, $x, $y, $z] = $m;
		$cubes[] = [$x, $y, $z];

		if (!isset($map[$z][$y])) { $map[$z][$y] = []; }
		if (!isset($map[$z])) { $map[$z] = []; }
		$map[$z][$y][$x] = '#';
	}

	$part1 = 0;
	foreach ($cubes as $cube) {
		[$x, $y, $z] = $cube;

		if (!isset($map[$z][$y][$x + 1])) { $part1++; }
		if (!isset($map[$z][$y][$x - 1])) { $part1++; }
		if (!isset($map[$z][$y + 1][$x])) { $part1++; }
		if (!isset($map[$z][$y - 1][$x])) { $part1++; }
		if (!isset($map[$z + 1][$y][$x])) { $part1++; }
		if (!isset($map[$z - 1][$y][$x])) { $part1++; }
	}

	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
