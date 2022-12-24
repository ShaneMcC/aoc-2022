#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputSparseMap();

	$_CACHE[0] = $map;
	$maxBlizzardTime = (max(array_keys($map)) - 1) * (max(array_keys($map[0])) - 1);

	function getMapAtTime($time) {
		global $_CACHE, $maxBlizzardTime;

		$time = $time % $maxBlizzardTime;
		if (isset($_CACHE[$time])) { return $_CACHE[$time]; }
		$map = getMapAtTime(max(0, $time - 1));

		$newMap = [];
		foreach ($map as $y => $row) {
			foreach ($row as $x => $cell) {
				if ($cell == '#') {
					if (!isset($newMap[$y])) { $newMap[$y] = []; }
					$newMap[$y][$x] = $cell;
				} else {
					foreach (str_split($cell) as $snow) {
						[$nX, $nY] = [$x, $y];
						if ($snow == '<') {
							$nX--;
						} else if ($snow == '>') {
							$nX++;
						} else if ($snow == 'v') {
							$nY++;
						} else if ($snow == '^') {
							$nY--;
						}

						if (isset($map[$nY][$nX]) && $map[$nY][$nX] == '#') {
							if ($snow == '<') {
								$nX = max(array_keys($row)) - 1;
							} else if ($snow == '>') {
								$nX = 1;
							} else if ($snow == 'v') {
								$nY = 1;
							} else if ($snow == '^') {
								$nY = max(array_keys($map)) - 1;
							}
						}

						if (!isset($newMap[$nY])) { $newMap[$nY] = []; }
						if (!isset($newMap[$nY][$nX])) { $newMap[$nY][$nX] = ''; }
						$newMap[$nY][$nX] .= $snow;
					}
				}
			}
		}

		$_CACHE[$time] = $newMap;
		return $newMap;
	}

	function getRouteCost($grid, $start, $end, $startTime = 0) {
		$next = [];
		$next[implode(',', $start)] = true;

		[$minX, $minY, $maxX, $maxY] = getBoundingBox($grid);

		for ($cost = $startTime; $cost < PHP_INT_MAX; $cost++) {
			$possible = [];
			$map = getMapAtTime($cost + 1);
			foreach (array_keys($next) as $n) {
				[$x, $y] = explode(',', $n);

				if ([$x, $y] == $end) { return $cost - $startTime; }

				foreach (getAllAdjacentCells($map, $x, $y, false, true) as [$pX, $pY]) {
					 // Blizzard in the space.
					if (isset($costs[$pY][$pX])) { continue; }
					// Wall, or something else.
					if (isset($map[$pY][$pX])) { continue; }
					// Out of bounds.
					if ($pY < $minY || $pY > $maxY || $pX < $minX || $pX > $maxX) { continue; }

					$possible[implode(',', [$pX, $pY])] = true;
				}
			}

			$next = $possible;
		}

		return FALSE;
	}

	$start = [1, 0];
	$end = [count($map[0]) - 1, count($map) - 1];
	$cost = getRouteCost($map, $start, $end);

	$part1 = $cost;
	echo 'Part 1: ', $part1, "\n";

	// Back to the start
	$cost += getRouteCost($map, $end, $start, $cost);
	// Back to the end again.
	$cost += getRouteCost($map, $start, $end, $cost);

	$part2 = $cost;
	echo 'Part 2: ', $part2, "\n";
