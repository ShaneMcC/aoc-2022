#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$map = [];
	$cubes = [];

	$minX = PHP_INT_MAX;
	$maxX = PHP_INT_MIN;
	$minY = PHP_INT_MAX;
	$maxY = PHP_INT_MIN;
	$minZ = PHP_INT_MAX;
	$maxZ = PHP_INT_MIN;

	foreach ($input as $line) {
		preg_match('#(.*),(.*),(.*)#SADi', $line, $m);
		[, $x, $y, $z] = $m;
		$cubes[] = [$x, $y, $z];

		if (!isset($map[$z][$y])) { $map[$z][$y] = []; }
		if (!isset($map[$z])) { $map[$z] = []; }
		$map[$z][$y][$x] = '#';

		$minX = (int)min($minX, $x);
		$maxX = (int)max($maxX, $x);
		$minY = (int)min($minY, $y);
		$maxY = (int)max($maxY, $y);
		$minZ = (int)min($minZ, $z);
		$maxZ = (int)max($maxZ, $z);
	}

	$directions = [['x' => 1, 'y' => 0, 'z' => 0],
	               ['x' => -1, 'y' => 0, 'z' => 0],
	               ['x' => 0, 'y' => 1, 'z' => 0],
	               ['x' => 0, 'y' => -1, 'z' => 0],
	               ['x' => 0, 'y' => 0, 'z' => 1],
	               ['x' => 0, 'y' => 0, 'z' => -1],
	              ];

	function getSurfaceArea($map, $cubes) {
		global $directions;

		$surfaceArea = 0;

		foreach ($cubes as $cube) {
			[$x, $y, $z] = $cube;

			foreach ($directions as $d) {
				if (isset($map[$z + $d['z']][$y + $d['y']][$x + $d['x']]) != isset($map[$z][$y][$x])) {
					$surfaceArea++;
				}
			}
		}

		return $surfaceArea;
	}

	function getReachableCubes($map, $startCube) {
		global $directions, $minX, $maxX, $minY, $maxY, $minZ, $maxZ;

		$outsideCubes = [];
		$queue = new SPLQueue();
		$queue->enqueue($startCube);
		while (!$queue->isEmpty()) {
			$cube = $queue->dequeue();
			[$x, $y, $z] = $cube;
			$id = $x . ',' . $y . ',' . $z;
			if (isset($outsideCubes[$id])) { continue; }
			$outsideCubes[$id] = [$x, $y, $z];
			if ($z < ($minZ - 1) || $z > ($maxZ + 1) || $y < ($minY - 1) || $y > ($maxY + 1) || $x < ($minX - 1) || $x > ($maxX + 1)) { continue; }

			foreach ($directions as $d) {
				[$x2, $y2, $z2] = [$x + $d['x'], $y + $d['y'], $z + $d['z']];
				$id2 = $x2 . ',' . $y2 . ',' . $z2;

				if (isset($map[$z][$y][$x]) == isset($map[$z2][$y2][$x2])) {
					$queue->enqueue([$x2, $y2, $z2]);
				}
			}
		}

		return $outsideCubes;
	}

	$part1 = getSurfaceArea($map, $cubes);
	echo 'Part 1: ', $part1, "\n";

	$outsideCubes = getReachableCubes($map, [$minX - 1, $minY - 1, $minZ - 1]);
	$part2 = getSurfaceArea($map, $outsideCubes);
	echo 'Part 2: ', $part2, "\n";
