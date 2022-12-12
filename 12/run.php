#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$grid = getInputMap();
	$start = findCells($grid, 'S')[0];
	$allA = findCells($grid, 'a');
	$end = findCells($grid, 'E')[0];
	$grid[$start[1]][$start[0]] = 'a';
	$grid[$end[1]][$end[0]] = 'z';

	function getCosts($grid, $start) {
		$costs = [];

		$queue = new SPLPriorityQueue();
		$queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
		$queue->insert([$start[0], $start[1], []], 0);

		while (!$queue->isEmpty()) {
			$q = $queue->extract();
			[$x, $y, $path] = $q['data'];
			$cost = abs($q['priority']);

			if (isset($costs[$y][$x])) { continue; }

			$costs[$y][$x] = $cost;

			foreach (getAdjacentCells($grid, $x, $y) as [$pX, $pY]) {
				if (ord($grid[$y][$x]) > (ord($grid[$pY][$pX]) + 1)) { continue; }
				if (isset($costs[$pY][$pX])) { continue; }

				$queue->insert([$pX, $pY, $path], -($cost + 1));
			}
		}

		return $costs;
	}

	$allCosts = getCosts($grid, $end);

	$part1 = $allCosts[$start[1]][$start[0]];
	echo 'Part 1: ', $part1, "\n";

	$part2 = $part1;
	foreach ($allA as $a) {
		$part2 = min($part2, $allCosts[$a[1]][$a[0]] ?? PHP_INT_MAX);
	}
	echo 'Part 2: ', $part2, "\n";
