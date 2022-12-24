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

	function getRouteCost($grid, $start, $end) {
		$visited = [];
		$queue = new SPLPriorityQueue();
		$queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
		$queue->insert([$start[0], $start[1], 0, 0], 0);

		[$minX, $minY, $maxX, $maxY] = getBoundingBox($grid);

		while (!$queue->isEmpty()) {
			$q = $queue->extract();
			[$x, $y, $cost, $waited] = $q['data'];
			// $cost = abs($q['priority']);

			echo json_encode([$x, $y, $cost, $waited, $queue->count()]), "\n";

			if ([$x, $y] == $end) { return $cost; }
			if ($waited > 3) { continue; } // Don't wait too long.

			$map = getMapAtTime($cost + 1);

			$moved = false;
			foreach (getAllAdjacentCells($map, $x, $y, false, false) as [$pX, $pY]) {
				 // Blizzard in the space.
				if (isset($costs[$pY][$pX])) { continue; }
				// Wall, or something else.
				if (isset($map[$pY][$pX])) { continue; }
				// Out of bounds.
				if ($pY < $minY || $pY > $maxY || $pX < $minX || $pX > $maxX) { continue; }

				// echo "\t", json_encode([$pX, $pY]), "\n";

				$moved = true;
				$queue->insert([$pX, $pY, $cost + 1, 0], -($cost + 1));
			}
			if (!$moved) {
				// Wait if we can't move.
				$queue->insert([$x, $y, $cost + 1, $waited + 1], -($cost + 1));
			}
		}

		return FALSE;
	}

	$start = [1, 0];
	$end = [count($map[0]) - 1, count($map) - 1];
	$cost = getRouteCost($map, $start, $end);

	var_dump($cost);

	$part1 = $cost;
	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
