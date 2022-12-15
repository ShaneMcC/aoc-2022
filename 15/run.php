#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$map = [];

	$minX = PHP_INT_MAX;
	$maxX = PHP_INT_MIN;

	$checkRow = isTest() ? 10 : 2000000;

	$sensors = [];
	$beacons = [];
	foreach ($input as $line) {
		preg_match('#Sensor at x=(.*), y=(.*): closest beacon is at x=(.*), y=(.*)#SADi', $line, $m);
		[, $sX, $sY, $bX, $bY] = $m;
		$distance = manhattan($sX, $sY, $bX, $bY);
		$sensors[$sX . ',' . $sY] = ['loc' => [$sX, $sY], 'beacon' => [$bX, $bY], 'distance' => $distance];

		$beacons[$bX . ',' . $bY] = true;

		$manhattanToCheck = $distance - manhattan($sX, $checkRow, $sX, $sY);

		$minX = min(min($minX, $sX - $manhattanToCheck), $bX);
		$maxX = max(max($maxX, $sX + $manhattanToCheck), $bX);
	}

	usort($sensors, fn($a, $b) => $a['loc'][0] <=> $b['loc'][0]);

	$y = $checkRow;
	$part1 = 0;
	for ($x = $minX; $x <= $maxX; $x++) {
		foreach ($sensors as $s) {
			$distanceToClosest = $s['distance'];
			$distanceToMe = manhattan($x, $y, $s['loc'][0], $s['loc'][1]);

			if ($distanceToMe <= $distanceToClosest) {
				// If this space is covered, others along this row
				// will be as well, so lets bypass those.
				$manhattanToTop = manhattan($s['loc'][0], $y, $s['loc'][0], $s['loc'][1]);
				$newX = $s['loc'][0] + ($s['distance'] - $manhattanToTop);

				$part1 += ($newX - $x) + 1;
				$x = $newX;
				break;
			}
		}
	}

	// Remove any beacons on this row.
	foreach (array_keys($beacons) as $b) {
		[$bX, $bY] = explode(',', $b);
		if ($bY == $y) { $part1--; }
	}
	echo 'Part 1: ', $part1, "\n";

	$min = 0;
	$max = isTest() ? 20 : 4000000;
	$part2 = 0;
	for ($y = $min; $y <= $max; $y++) {
		$x = 0;
		foreach ($sensors as $s) {
			$distanceToClosest = $s['distance'];
			$manhattanToTop = manhattan($s['loc'][0], $y, $s['loc'][0], $s['loc'][1]);
			$minX = $s['loc'][0] - ($distanceToClosest - $manhattanToTop);
			$maxX = $s['loc'][0] + ($distanceToClosest - $manhattanToTop);
			$testX = $distanceToClosest - $manhattanToTop;

			if ($minX <= $x) {
				$x = max($x, $maxX);
				if ($x >= $max) { continue 2; }
			}
		}

		if ($x <= $max) {
			$part2 = (4000000 * $x) + $y;
			break;
		}
	}

	echo 'Part 2: ', $part2, "\n";
