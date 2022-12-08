#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	$part1 = $part2 = 0;
	foreach ($map as $y => $row) {
		foreach ($row as $x => $tree) {
			$column = array_column($map, $x);

			$before = array_slice($row, 0, $x);
			$after = array_slice($row, $x + 1);
			$above = array_slice($column, 0, $y);
			$below = array_slice($column, $y + 1);

			// Filter array to only contain trees that are taller than us
			// we are visible if this results in an empty array.
			$visibleCheck = fn($a) => $a >= $tree;

			$visible = empty(array_filter($before, $visibleCheck))
			        || empty(array_filter($after, $visibleCheck))
			        || empty(array_filter($above, $visibleCheck))
			        || empty(array_filter($below, $visibleCheck));

			if ($visible) { $part1++; }

			// Makes the logic easier
			$before = array_reverse($before);
			$above = array_reverse($above);

			// Reduce the array to a counter of how many trees we can see.
			// We increment the first part of our carry array for each tree
			// we can see as long as the second part is true.
			// The second part is true for as long as we have only ever seen
			// trees shorter than us, once we see a tree the same height or
			// larger then this gets set to false and we stop doing anything
			// else and just keep returning the same carry.
			$scenicCheck = fn($c, $i) => $c[1] ? [$c[0] + 1, $i < $tree] : $c;

			$before = array_reduce($before, $scenicCheck, [0, true])[0];
			$after = array_reduce($after, $scenicCheck, [0, true])[0];
			$above = array_reduce($above, $scenicCheck, [0, true])[0];
			$below = array_reduce($below, $scenicCheck, [0, true])[0];

			$scenic = $before * $after * $above * $below;
			$part2 = max($part2, $scenic);
		}
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
