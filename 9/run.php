#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$tail = [0, 0];
	$head = [0, 0];

	$directions = ['U' => [0, 1],
	               'D' => [0, -1],
	               'R' => [1, 0],
	               'L' => [-1, 0],
	              ];

	$tailPositions = [];
	foreach ($input as $in) {
		[$d, $c] = explode(' ', $in, 2);

		if (isDebug()) { echo $in, "\n"; }

		for ($i = 0; $i < $c; $i++) {
			// Move the head
			$head[0] += $directions[$d][0];
			$head[1] += $directions[$d][1];

			$yDiff = abs($tail[1] - $head[1]);
			$xDiff = abs($tail[0] - $head[0]);

			// Move the tail to account for the head.
			if ($yDiff == 2) {
				if ($tail[1] > $head[1]) { $tail[1]--; }
				if ($tail[1] < $head[1]) { $tail[1]++; }

				if ($xDiff == 1) {
					if ($tail[0] > $head[0]) { $tail[0]--; }
					if ($tail[0] < $head[0]) { $tail[0]++; }
				}
			}

			if ($xDiff == 2) {
				if ($tail[0] > $head[0]) { $tail[0]--; }
				if ($tail[0] < $head[0]) { $tail[0]++; }

				if ($yDiff == 1) {
					if ($tail[1] > $head[1]) { $tail[1]--; }
					if ($tail[1] < $head[1]) { $tail[1]++; }
				}
			}

			if (isDebug()) { echo "\t", json_encode(['head' => $head, 'tail' => $tail]), "\n"; }

			$tailPositions[json_encode($tail)] = true;
		}
	}

	$part1 = count($tailPositions);
	echo 'Part 1: ', $part1, "\n";
