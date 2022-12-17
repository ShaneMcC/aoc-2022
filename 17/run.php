#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	$shapes = [];
	$shapes[] = ['####']; // Dash
	$shapes[] = ['.#.', '###', '.#.']; // Plus
	$shapes[] = ['..#', '..#', '###']; // Backwards-L
	$shapes[] = ['#', '#', '#', '#']; // Pipe
	$shapes[] = ['##', '##']; // Square

	$map = [];

	$shapeIndex = 0;
	$jetIndex = 0;

	function getNextRock() {
		global $shapes, $shapeIndex;
		$shape = $shapes[$shapeIndex];
		$shapeIndex = ($shapeIndex + 1) % count($shapes);
		return $shape;
	}

	function getNextJet() {
		global $input, $jetIndex;
		$jet = $input[$jetIndex];
		$jetIndex = ($jetIndex + 1) % strlen($input);
		return $jet;
	}

	function dropRock(&$map) {
		$highestTop = -1;
		for ($y = count($map) - 1; $y >= 0; $y--) {
			for ($x = 0; $x < 7; $x++) {
				if (isset($map[$y][$x])) {
					$highestTop = $y;
					break 2;
				}
			}
		}

		$rock = getNextRock();
		$rockHeight = count($rock);
		$rockWidth = strlen($rock[0]);
		$rockTop = ($highestTop + 3) + $rockHeight;
		$rockLeft = 2;
		$canFall = true;

		if (isDebug()) { drawCave($map, $rock, [$rockLeft, $rockTop]); }
		while ($canFall) {
			$jet = getNextJet();

			$canLeft = true;
			$canRight = true;
			$canDown = true;

			// Check if any part of the shape would overlap if we moved it
			// left or right.
			for ($rY = 0; $rY < $rockHeight; $rY++) {
				for ($rX = 0; $rX < $rockWidth; $rX++) {
					if ($rock[$rY][$rX] == '#') {
						if (isset($map[$rockTop - $rY][$rockLeft + $rX + 1])) {
							$canRight = false;
						}
						if (isset($map[$rockTop - $rY][$rockLeft + $rX - 1])) {
							$canLeft = false;
						}
					}
				}
			}

			// Push Rock.
			if ($jet == '<' && $canLeft) {
				$rockLeft = max(0, $rockLeft - 1);
			} else if ($jet == '>' && $canRight) {
				$rockLeft = min(7 - $rockWidth, $rockLeft + 1);
			} else {
				if (isDebug()) { echo 'stay at '; }
			}

			if (isDebug()) { echo $jet, ' to ', $rockLeft, "\n"; }

			// Check if rock can fall.
			$canFall = ($rockTop - $rockHeight) != -1;
			for ($rY = 0; $rY < $rockHeight; $rY++) {
				for ($rX = 0; $rX < $rockWidth; $rX++) {
					if ($rock[$rY][$rX] == '#') {
						if (isset($map[$rockTop - $rY - 1][$rockLeft + $rX])) {
							$canFall = false;
						}
					}
				}
			}

			if ($canFall) {
				$rockTop--;
				if (isDebug()) {
					echo 'fall', "\n";
					drawCave($map, $rock, [$rockLeft, $rockTop]);
				}
			} else {
				if (isDebug()) {
					echo 'rest', "\n";
					drawCave($map, $rock, [$rockLeft, $rockTop]);
				}
				for ($rY = 0; $rY < $rockHeight; $rY++) {
					for ($rX = 0; $rX < $rockWidth; $rX++) {
						if ($rock[$rY][$rX] == '#') {
							$map[$rockTop - $rY][$rockLeft + $rX] = $rock[$rY][$rX];
						}
					}
				}
			}
		}
	}


	function drawCave($map, $rock = NULL, $rockXY = NULL) {
		if ($rock != NULL) {
			$rockPos = [];
			$rockHeight = count($rock);
			$rockWidth = strlen($rock[0]);
			[$rockLeft, $rockTop] = $rockXY;
			for ($rY = 0; $rY < $rockHeight; $rY++) {
				for ($rX = 0; $rX < $rockWidth; $rX++) {
					if ($rock[$rY][$rX] == '#') {
						$rockPos[$rockTop - $rY][$rockLeft + $rX] = true;
					}
				}
			}
		}

		for ($y = count($map) + 6; $y >= 0; $y--) {
			echo '|';
			for ($x = 0; $x < 7; $x++) {
				if (isset($rockPos[$y][$x]) && isset($map[$y][$x])) { die('X'); }

				$bit = isset($rockPos[$y][$x]) ? '@' : (isset($map[$y][$x]) ? '#' : '.');
				echo $bit;
			}
			echo '|';
			echo "\n";
		}
		echo '+-------+', "\n";
	}

	function getMapHeight($map, $wanted = 2022) {
		global $shapeIndex, $jetIndex;

		$shapeIndex = 0;
		$jetIndex = 0;
		$offset = 0;
		$foundCycle = false;
		$cycles = [];

		$seen = [];
		for ($i = 0; $i < $wanted; $i++) {
			// echo $i, "\n";
			dropRock($map);

			if (!$foundCycle) {
				$top = implode('', array_map(fn($i) => isset($map[count($map) - 1][$i]) ? '#' : '.', range(0, 6)));
				// $code = json_encode([$top, $shapeIndex, $jetIndex]);
				$code = json_encode([$top, $shapeIndex, $jetIndex]);
				if (isset($seen[$code])) {
					$cycleLength = ($i - $seen[$code][0]);
					$cycleHeight = (count($map) -$seen[$code][1]);

					echo 'Cycle Found at: ', $i, ' (Same as: ', $seen[$code][0], ') => ', ($i - $seen[$code][0]), ' Height is: ', count($map), ' => change: ', count($map) -$seen[$code][1], "\n";

					$cycles[] = $cycleLength;
					if (count($cycles) > 1 && $cycles[count($cycles) - 1] == $cycles[count($cycles) - 2]) {
						$foundCycle = true;
						$diff = floor(($wanted - $i) / $cycleLength);
						$i += $cycleLength * $diff;
						$offset += $cycleHeight * $diff;
					} else {
						$seen = [];
					}
				}

				$seen[$code] = [$i, count($map)];
			}

			if (isDebug()) {
				drawCave($map);
				echo "=====================================================================", "\n";
			}
		}

		return count($map) + $offset;
	}

	$part1 = getMapHeight($map, 2022);
	echo 'Part 1: ', $part1, "\n";

	$part2 = getMapHeight($map, 1000000000000);
	echo 'Part 2: ', $part2, "\n";

