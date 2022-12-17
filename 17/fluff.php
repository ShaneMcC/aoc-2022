<?php

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
