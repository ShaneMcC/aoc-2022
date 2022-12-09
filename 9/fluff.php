<?php

	function getRopeMap($rope, $minX = -20, $maxX = 20, $minY = -20, $maxY = 20) {
		$map = [];
		for ($y = $maxY - 1; $y >= $minY; $y--) {
			for ($x = $minX; $x < $maxX; $x++) {
				$out = [$x, $y] == [0, 0] ? 's' : '.';
				for ($k = 0; $k < count($rope); $k++) {
					if ($rope[$k] == [$x, $y]) {
						$out = $k;
						break;
					}
				}

				if (!isset($map[$y])) { $map[$y] = []; }
				$map[$y][$x] = $out;
			}
		}

		return $map;
	}


	function getPositionMap($positions, $minX = -20, $maxX = 20, $minY = -20, $maxY = 20) {
		$map = [];
		for ($y = $maxY - 1; $y >= $minY; $y--) {
			for ($x = $minX; $x < $maxX; $x++) {
				$out = [$x, $y] == [0, 0] ? 's' : '.';
				if ($out != 's' && isset($positions[implode(',', [$x, $y])])) {
					$out = '#';
				}

				if (!isset($map[$y])) { $map[$y] = []; }
				$map[$y][$x] = $out;
			}
		}

		return $map;
	}
