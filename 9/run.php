#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$tail = [0, 0];
	$head = [0, 0];

	$rope = array_fill(0, 10, [0, 0]);

	$directions = ['U' => [0, 1],
	               'D' => [0, -1],
	               'R' => [1, 0],
	               'L' => [-1, 0],
	              ];

	function moveKnot($head, $tail) {
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

		return $tail;
	}

	$tailPositions = [1 => [], 9 => []];

	if (isDebug()) {
		drawMap(getRopeMap($rope), '.', true, 'Initial State');
	}

	foreach ($input as $in) {
		[$d, $c] = explode(' ', $in, 2);

		for ($i = 0; $i < $c; $i++) {
			// Move the head
			$rope[0][0] += $directions[$d][0];
			$rope[0][1] += $directions[$d][1];

			for ($k = 1; $k < count($rope); $k++) {
				$rope[$k] = moveKnot($rope[$k - 1], $rope[$k]);
			}

			$tailPositions[1][implode(',', $rope[1])] = true;
			$tailPositions[9][implode(',', $rope[9])] = true;
		}

		if (isDebug()) {
			drawMap(getRopeMap($rope), '.', true, $in);
		}
	}

	$part1 = count($tailPositions[1]);
	echo 'Part 1: ', $part1, "\n";

	$part2 = count($tailPositions[9]);
	echo 'Part 2: ', $part2, "\n";
