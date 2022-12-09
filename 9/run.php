#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

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

	$part1 = [];
	$part2 = [];

	// if (isDebug()) { drawMap(getRopeMap($rope), true, 'Initial State');	}

	foreach ($input as $in) {
		[$d, $c] = explode(' ', $in, 2);

		for ($i = 0; $i < $c; $i++) {
			// Move the head
			$rope[0][0] += $directions[$d][0];
			$rope[0][1] += $directions[$d][1];

			for ($k = 1; $k < count($rope); $k++) {
				$rope[$k] = moveKnot($rope[$k - 1], $rope[$k]);
			}

			$part1[implode(',', $rope[1])] = true;
			$part2[implode(',', $rope[count($rope) - 1])] = true;
		}

		// if (isDebug()) { drawMap(getRopeMap($rope), true, $in); }
	}

	if (isDebug()) {
		drawMap(getPositionMap($part2), true, $in);
	}

	echo 'Part 1: ', count($part1), "\n";
	echo 'Part 2: ', count($part2), "\n";

