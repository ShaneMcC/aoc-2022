<?php

	function getDisplayCharacater(&$map, $x, $y, $fancyWalls = true) {
		if (!isset($map[$y][$x])) {
			echo ' ';
		} else if ($map[$y][$x] == 'o') {
			echo "\033[1;33m";
			echo '█';
			echo "\033[0m";
		} else if ($map[$y][$x] == '#') {
			echo "\033[1;31m";

			if (!$fancyWalls) {
				echo '█';
			} else if (($map[$y][$x + 1] ?? '') != '#' && ($map[$y][$x - 1] ?? '') == '#' && ($map[$y + 1][$x] ?? '') == '#' && ($map[$y - 1][$x] ?? '') != '#') {
				// Top Right Corner.
				echo '┓';
			} else if (($map[$y][$x + 1] ?? '') == '#' && ($map[$y][$x - 1] ?? '') != '#' && ($map[$y + 1][$x] ?? '') == '#' && ($map[$y - 1][$x] ?? '') != '#') {
				// Top Left Corner
				echo '┏';
			} else if (($map[$y][$x + 1] ?? '') != '#' && ($map[$y][$x - 1] ?? '') == '#' &&($map[$y + 1][$x] ?? '') != '#' && ($map[$y - 1][$x] ?? '') == '#') {
				// Bottom Right Corner.
				echo '┛';
			} else if (($map[$y][$x + 1] ?? '') == '#' && ($map[$y][$x - 1] ?? '') != '#' &&($map[$y + 1][$x] ?? '') != '#' && ($map[$y - 1][$x] ?? '') == '#') {
				// Bottom Left Corner
				echo '┗';
			} else if (($map[$y + 1][$x] ?? '') != '#' && ($map[$y - 1][$x] ?? '') != '#') {
				// Top/Bottom Edge
				echo '━';
			} else if (($map[$y][$x + 1] ?? '') != '#' && ($map[$y][$x - 1] ?? '') != '#') {
				// Left/Right Edge
				echo '┃';
			} else if (($map[$y - 1][$x] ?? '') == '#') {
				// Top/Bottom Edge With an Upright
				echo '┻';
			} else {
				// Unknown.
				echo '@';
			}
			echo "\033[0m";
		}
	}

	function drawSandMap($map, $source, $floor = false) {
		$minX = PHP_INT_MAX;
		$maxX = PHP_INT_MIN;
		foreach ($map as $m) {
			$maxX = max($maxX, max(array_keys($m)));
			$minX = min($minX, min(array_keys($m)));
		}

		for ($y = 0; $y <= ($floor != false ? $floor : max(array_keys($map))); $y++) {
			for ($x = $minX; $x <= $maxX; $x++) {
				if ($floor != false && $y == $floor) { echo '┉'; }
				else if ($source == [$x, $y]) { echo '▄'; }
				else { getDisplayCharacater($map, $x, $y); }
			}
			echo "\n";
		}
	}
