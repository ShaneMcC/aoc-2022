#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	$part1 = 0;
	foreach ($map as $y => $row) {
		foreach ($row as $x => $tree) {
			$before = array_slice($row, 0, $x);
			$after = array_slice($row, $x + 1);

			$column = array_column($map, $x);
			$above = array_slice($column, 0, $y);
			$below = array_slice($column, $y + 1);

			$visible = empty(array_filter($before, fn($a) => $a >= $tree))
			        || empty(array_filter($after, fn($a) => $a >= $tree))
			        || empty(array_filter($above, fn($a) => $a >= $tree))
			        || empty(array_filter($below, fn($a) => $a >= $tree));

			if ($visible) {
				$part1++;
			}
		}
	}

	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
