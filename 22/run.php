#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$changes = ['N' => ['d' => [0, -1], 'L' => 'W', 'R' => 'E', 's' => '^', 'num' => 3],
	            'S' => ['d' => [0, +1], 'L' => 'E', 'R' => 'W', 's' => 'v', 'num' => 1],
	            'E' => ['d' => [+1, 0], 'L' => 'N', 'R' => 'S', 's' => '>', 'num' => 0],
	            'W' => ['d' => [-1, 0], 'L' => 'S', 'R' => 'N', 's' => '<', 'num' => 2],
	           ];

	$instructions = array_pop($input);
	$map = [];
	foreach ($input as $row) { $map[] = str_split($row); }

	function getFirstTile($row, $valid = ['.', '#']) {
		for ($i = 0; $i < count($row); $i++) {
			if (in_array($row[$i], $valid)) { return $i; }
		}
		return null;
	}

	function getLastTile($row, $valid = ['.', '#']) {
		for ($i = count($row) - 1; $i != 0 ; $i--) {
			if (in_array($row[$i], $valid)) { return $i; }
		}
		return null;
	}

	function splitInstructions($instr) {
		$result = [];
		$next = '';
		for ($i = 0; $i < strlen($instr); $i++) {
			if (is_numeric($instr[$i])) {
				$next .= $instr[$i];
			} else {
				$result[] = $next;
				$next = '';
				$result[] = $instr[$i];
			}
		}
		if (!empty($next)) { $result[] = $next; }
		return $result;
	}

	$finalMap = $map;

	$position = [getFirstTile($map[0], ['.']), 0];
	$direction = 'E';

	$finalMap[$position[1]][$position[0]] = $changes[$direction]['s'];

	foreach (splitInstructions($instructions) as $in) {
		if ($in == 'L' || $in == 'R') {
			$direction = $changes[$direction][$in];
		} else {
			for ($i = 0; $i < $in; $i++) {
				$x = $position[0] + $changes[$direction]['d'][0];
				$y = $position[1] + $changes[$direction]['d'][1];

				if (isset($map[$y][$x]) && $map[$y][$x] != ' ') {
					if ($map[$y][$x] == '.') {
						// Empty tile, we can move here.
						$newPosition = [$x, $y];
					}
				} else {
					// Find the next tile.
					if ($direction == 'N') {
						$y = getLastTile(array_column($map, $position[0]));
					} else if ($direction == 'S') {
						$y = getFirstTile(array_column($map, $position[0]));
					} else if ($direction == 'E') {
						$x = getFirstTile($map[$position[1]]);
					} else if ($direction == 'W') {
						$x = getLastTile($map[$position[1]]);
					}

					if ($map[$y][$x] == '.') {
						// Empty tile, we can move here.
						$newPosition = [$x, $y];
					}
				}
				$finalMap[$position[1]][$position[0]] = $changes[$direction]['s'];
				$position = $newPosition;
			}
		}
		$finalMap[$position[1]][$position[0]] = $changes[$direction]['s'];
	}

	$part1 = (1000 * ($position[1] + 1)) + (4 * ($position[0] + 1)) + $changes[$direction]['num'];
	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
