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


	function getPositionMap($positions, $minX = null, $maxX = null, $minY = null, $maxY = null) {

		$actualMinX = PHP_INT_MAX;
		$actualMaxX = PHP_INT_MIN;
		$actualMinY = PHP_INT_MAX;
		$actualMaxY = PHP_INT_MIN;
		foreach (array_keys($positions) as $p) {
			[$x, $y] = explode(',', $p);
			$actualMinX = min($actualMinX, $x);
			$actualMaxX = max($actualMaxX, $x);
			$actualMinY = min($actualMinY, $y);
			$actualMaxY = max($actualMaxY, $y);
		}

		$minX ??= $actualMinX - 2;
		$maxX ??= $actualMaxX + 2;
		$minY ??= $actualMinY - 2;
		$maxY ??= $actualMaxY + 2;

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
