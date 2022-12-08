#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	$part1 = $part2 = 0;
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

			if ($visible) { $part1++; }

			$before = array_reverse($before);
			$above = array_reverse($above);

			$before = array_reduce($before, fn($c, $i) => $c[1] ? [$c[0] + 1, $i < $tree] : $c, [0, true])[0];
			$after = array_reduce($after, fn($c, $i) => $c[1] ? [$c[0] + 1, $i < $tree] : $c, [0, true])[0];
			$above = array_reduce($above, fn($c, $i) => $c[1] ? [$c[0] + 1, $i < $tree] : $c, [0, true])[0];
			$below = array_reduce($below, fn($c, $i) => $c[1] ? [$c[0] + 1, $i < $tree] : $c, [0, true])[0];

			$scenic = $before * $after * $above * $below;
			$part2 = max($part2, $scenic);
		}
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
