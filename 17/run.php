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
		for ($y = 0; $y < count($map); $y++) {
			for ($x = 0; $x < 7; $x++) {
				if (isset($map[$y][$x])) {
					$highestTop = $y;
					break;
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

	for ($i = 0; $i < 2022; $i++) {
		dropRock($map);
		 if (isDebug()) {
			drawCave($map);
			echo "=====================================================================", "\n";
		}
	}

	$part1 = count($map);
	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
