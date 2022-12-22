#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$changes = ['E' => ['d' => [+1, 0], 'L' => 'N', 'R' => 'S', 's' => '>', 'num' => 0],
	            'S' => ['d' => [0, +1], 'L' => 'E', 'R' => 'W', 's' => 'v', 'num' => 1],
	            'W' => ['d' => [-1, 0], 'L' => 'S', 'R' => 'N', 's' => '<', 'num' => 2],
	            'N' => ['d' => [0, -1], 'L' => 'W', 'R' => 'E', 's' => '^', 'num' => 3],
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

	// This assumes a map shape:
	//
	//  ##
	//  #
	// ##
	// #
	//
	// Which works for input, but not sample 'cos eric hates us?
	function getNextSpace($x, $y, $direction) {
		global $changes;
		[$dX, $dY] = $changes[$direction]['d'];

		if ($dY == -1) {
			if ($y == 0 && (int)floor($x/50) == 1) {
				return [0, 150 + ($x % 50), 'E'];
			}
			if ($y == 0 && (int)floor($x/50) == 2) {
				return [($x % 50), 199, 'N'];
			}
			if ($y == 100 && (int)floor($x/50) == 0) {
				return [50, 50 + ($x % 50), 'E'];
			}
		} else if ($dX == 1) {
			if ($x == 149 && (int)floor($y/50) == 0) {
				return [99, 149 - ($y % 50), 'W'];
			}
			if ($x == 99 && (int)floor($y/50) == 1) {
				return [100 + ($y % 50), 49, 'N'];
			}
			if ($x == 99 && (int)floor($y/50) == 2) {
				return [149, 49 - ($y % 50), 'W'];
			}
			if ($x == 49 && (int)floor($y/50) == 3) {
				return [50 + ($y % 50), 149, 'N'];
			}
		} else if ($dY == 1) {
			if ($y == 199 && (int)floor($x/50) == 0) {
				return [100 + ($x % 50), 0, 'S'];
			}
			if ($y == 149 && (int)floor($x/50) == 1) {
				return [49, 150 + ($x % 50), 'W'];
			}
			if ($y == 49 && (int)floor($x/50) == 2) {
				return [99, 50 + ($x % 50), 'W'];
			}
		} else if ($dX == -1) {
			if ($x == 0 && (int)floor($y/50) == 3) {
				return [50 + ($y % 50), 0, 'S'];
			}
			if ($x == 0 && (int)floor($y/50) == 2) {
				return [50, 49 - ($y % 50), 'E'];
			}
			if ($x == 50 && (int)floor($y/50) == 1) {
				return [($y % 50), 100, 'S'];
			}
			if ($x == 50 && (int)floor($y/50) == 0) {
				return [0, 149 - ($y % 50), 'E'];
			}
		}

		return [$x + $dX, $y + $dY, $direction];
	}

	function moveAroundMap($map, $start, $facing, $part2 = false) {
		global $changes, $instructions, $cubes;

		$position = $start;
		$direction = $facing;

		foreach (splitInstructions($instructions) as $in) {
			if ($in == 'L' || $in == 'R') {
				$direction = $changes[$direction][$in];
			} else {
				for ($i = 0; $i < $in; $i++) {
					[$myX, $myY] = $position;

					if ($part2) {
						[$newX, $newY, $newDirection] = getNextSpace($myX, $myY, $direction);
						if ($map[$newY][$newX] == '.') {
							$position = [$newX, $newY];
							$direction = $newDirection;
						}
					} else {
						[$dX, $dY] = $changes[$direction]['d'];
						[$x, $y] = [$myX + $dX, $myY + $dY];
						if (!isset($map[$y][$x]) || $map[$y][$x] == ' ') {
							if ($direction == 'N') {
								$y = getLastTile(array_column($map, $myX));
							} else if ($direction == 'S') {
								$y = getFirstTile(array_column($map, $myX));
							} else if ($direction == 'E') {
								$x = getFirstTile($map[$myY]);
							} else if ($direction == 'W') {
								$x = getLastTile($map[$myY]);
							}
						}

						if ($map[$y][$x] == '.') {
							$position = [$x, $y];
						}
					}
				}
			}
		}

		return [$position, $direction];
	}

	$start = [getFirstTile($map[0], ['.']), 0];
	$facing = 'E';

	[$position, $direction] = moveAroundMap($map, $start, $facing, false);
	$part1 = (1000 * ($position[1] + 1)) + (4 * ($position[0] + 1)) + $changes[$direction]['num'];
	echo 'Part 1: ', $part1, "\n";

	[$position, $direction] = moveAroundMap($map, $start, $facing, true);
	$part2 = (1000 * ($position[1] + 1)) + (4 * ($position[0] + 1)) + $changes[$direction]['num'];
	echo 'Part 2: ', $part2, "\n";
