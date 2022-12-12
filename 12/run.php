#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$grid = getInputMap();
	$start = [];
	$allA = [];
	$end = [];

	foreach ($grid as $y => $row) {
		foreach ($row as $x => $cell) {
			if ($cell == 'S' ) { $start = [$x, $y]; $grid[$y][$x] = 'a'; }
			if ($cell == 'E') { $end = [$x, $y]; $grid[$y][$x] = 'z'; }
			if ($cell == 'a' ) { $allA[] = [$x, $y]; }
		}
	}

	function getCost($grid, $start, $end, $max = PHP_INT_MAX) {
		$costs = [];

		$queue = new SPLPriorityQueue();
		$queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
		$queue->insert([$start[0], $start[1], []], 0);

		while (!$queue->isEmpty()) {
			$q = $queue->extract();

			list($x, $y, $path) = $q['data'];

			// SPLPriorityQueue treats higher numbers as higher priority,
			// so we using negatives when we insert, so get the real value here.
			$cost = abs($q['priority']);
			if ($cost >= $max) { continue; }

			// If we've visited here before then this is a longer-cost path so
			// we can ignore it.
			if (isset($costs[$y][$x])) { continue; }

			if (isDebug()) { $path[] = [$x, $y]; }
			$costs[$y][$x] = ['cost' => $cost, 'path' => $path];

			// Try and visit anywhere that we can visit
			foreach (getAdjacentCells($grid, $x, $y) as [$pX, $pY]) {
				// If it's valid...
				if (ord($grid[$pY][$pX]) > (ord($grid[$y][$x]) + 1)) { continue; }
				if (isset($costs[$pY][$pX])) { continue; }

				$queue->insert([$pX, $pY, $path], -($cost + 1));
			}
		}

		return $costs;
	}

	$costs = getCost($grid, $start, $end);
	$part1 = $costs[$end[1]][$end[0]]['cost'];
	echo 'Part 1: ', $part1, "\n";

	$bestA = PHP_INT_MAX;
	foreach ($allA as $a) {
		$costs = getCost($grid, $a, $end, $bestA);
		$bestA = min($bestA, ($costs[$end[1]][$end[0]]['cost'] ?? PHP_INT_MAX));
	}

	$part2 = $bestA;
	echo 'Part 2: ', $part2, "\n";
