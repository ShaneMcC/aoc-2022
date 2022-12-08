#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	function hasTaller($row, $height) {
		foreach ($row as $tree) {
			if ($tree >= $height) {
				return true;
			}
		}
		return false;
	}

	function getVisibleCount($row, $height) {
		$count = 0;
		foreach ($row as $tree) {
			$count++;
			if ($tree >= $height) { break; }
		}
		return $count;
	}

	$part1 = $part2 = 0;
	foreach ($map as $y => $row) {
		foreach ($row as $x => $tree) {
			$column = array_column($map, $x);

			$before = array_slice($row, 0, $x);
			$after = array_slice($row, $x + 1);
			$above = array_slice($column, 0, $y);
			$below = array_slice($column, $y + 1);

			$hidden = hasTaller($before, $tree)
			          && hasTaller($after, $tree)
			          && hasTaller($above, $tree)
			          && hasTaller($below, $tree);

			if (!$hidden) { $part1++; }

			$scenic = getVisibleCount(array_reverse($before), $tree)
			          * getVisibleCount($after, $tree)
			          * getVisibleCount(array_reverse($above), $tree)
			          * getVisibleCount($below, $tree);
			$part2 = max($part2, $scenic);
		}
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
