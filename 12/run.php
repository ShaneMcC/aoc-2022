#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$grid = getInputMap();
	$start = findCells($grid, 'S')[0];
	$allA = findCells($grid, 'a');
	$end = findCells($grid, 'E')[0];
	$grid[$start[1]][$start[0]] = 'a';
	$grid[$end[1]][$end[0]] = 'z';

	function getCost($grid, $start, $end, $max = PHP_INT_MAX, $includePath = false) {
		$costs = [];

		$queue = new SPLPriorityQueue();
		$queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
		$queue->insert([$start[0], $start[1], []], 0);

		while (!$queue->isEmpty()) {
			$q = $queue->extract();
			[$x, $y, $path] = $q['data'];
			$cost = abs($q['priority']);
			if ($cost >= $max) { continue; }

			if (isset($costs[$y][$x])) { continue; }

			if ($includePath) { $path[] = [$x, $y]; }
			$costs[$y][$x] = ['cost' => $cost, 'path' => $path];

			foreach (getAdjacentCells($grid, $x, $y) as [$pX, $pY]) {
				if (ord($grid[$pY][$pX]) > (ord($grid[$y][$x]) + 1)) { continue; }
				if (isset($costs[$pY][$pX])) { continue; }

				$queue->insert([$pX, $pY, $path], -($cost + 1));
			}
		}

		return $costs[$end[1]][$end[0]] ?? FALSE;
	}

	$part1 = getCost($grid, $start, $end)['cost'];
	echo 'Part 1: ', $part1, "\n";

	$part2 = $part1;
	foreach ($allA as $a) {
		$part2 = min($part2, getCost($grid, $a, $end, $part2)['cost'] ?? PHP_INT_MAX);
	}
	echo 'Part 2: ', $part2, "\n";
