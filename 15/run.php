#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$map = [];

	$minX = PHP_INT_MAX;
	$maxX = PHP_INT_MIN;

	$sensors = [];
	$beacons = [];
	foreach ($input as $line) {
		preg_match('#Sensor at x=(.*), y=(.*): closest beacon is at x=(.*), y=(.*)#SADi', $line, $m);
		[, $sX, $sY, $bX, $bY] = $m;
		$distance = manhattan($sX, $sY, $bX, $bY);
		$sensors[$sX . ',' . $sY] = ['loc' => [$sX, $sY], 'beacon' => [$bX, $bY], 'distance' => $distance];

		if (!isset($beacons[$bX . ',' . $bY])) { $beacons[$bX . ',' . $bY] = ['sensors' => []]; }
		$beacons[$bX . ',' . $bY]['sensors'][] = [$sX, $sY];

		$minX = min(min($minX, $sX - $distance), $bX);
		$maxX = max(max($maxX, $sX + $distance), $bX);
	}

	$y = isTest() ? 10 : 2000000;
	$part1 = 0;
	for ($x = $minX; $x <= $maxX; $x++) {
		if (isset($beacons[$x . ',' . $y])) { continue; }
		if (isset($sensors[$x . ',' . $y])) { continue; }

		foreach ($sensors as $s) {
			$distanceToClosest = $s['distance'];
			$distanceToMe = manhattan($x, $y, $s['loc'][0], $s['loc'][1]);

			if ($distanceToMe <= $distanceToClosest) {
				$part1++;
				break;
			}
		}
	}

	echo 'Part 1: ', $part1, "\n";

	$min = 0;
	$max = isTest() ? 20 : 4000000;
	for ($y = $min; $y <= $max; $y++) {
		for ($x = $min; $x <= $max; $x++) {
			$covered = false;
			foreach ($sensors as $s) {
				$distanceToClosest = $s['distance'];
				$distanceToMe = manhattan($x, $y, $s['loc'][0], $s['loc'][1]);

				if ($distanceToMe <= $distanceToClosest) {
					$covered = true;

					// If this space is covered, a others along this row
					// will be as well, so lets bypass those.
					$manhattanToTop = manhattan($s['loc'][0], $y, $s['loc'][0], $s['loc'][1]);
					$x = $s['loc'][0] + ($s['distance'] - $manhattanToTop);

					break;
				}
			}

			if (!$covered) {
				$part2 = (4000000 * $x) + $y;
				echo json_encode([$x, $y]), "\n";
				break 2;
			}
		}
	}

	echo 'Part 2: ', $part2, "\n";
