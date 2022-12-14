#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$source = [500, 0];

	$map = [];
	$lines = [];
	foreach ($input as $line) {
		preg_match_all('#(\d+),(\d+)#Si', $line, $m);

		$line = [];
		for ($i = 0; $i < count($m[0]); $i++) {
			$line[] = [$m[1][$i], $m[2][$i]];
		}

		$lines[] = $line;
	}

	foreach ($lines as $line) {
		$prev = null;
		foreach ($line as $bit) {
			if ($prev == null) { $prev = $bit; continue; }

			[$pX, $pY] = $prev;
			[$bX, $bY] = $bit;

			if ($pX == $bX) {
				for ($y = min($pY, $bY); $y <= max($pY, $bY); $y++) {
					if (!isset($map[$y])) { $map[$y] = []; }
					$map[$y][$bX] = '#';
				}
			} else if ($pY == $bY) {
				if (!isset($map[$bY])) { $map[$bY] = []; }
				for ($x = min($pX, $bX); $x <= max($pX, $bX); $x++) {
					$map[$bY][$x] = '#';
				}
			}

			$prev = $bit;
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
				if ($floor != false && $y == $floor) { echo '#'; }
				else { echo isset($map[$y][$x]) ? $map[$y][$x] : ($source == [$x, $y] ? 'x' : '.'); }
			}
			echo "\n";
		}
	}

	function addSand(&$map, $source, $floor = false, $count = 0) {
		$previousLoc = [];
		while (true) {
			$loc = empty($previousLoc) ? $source : array_pop($previousLoc);
			while (true) {
				[$x, $y] = $loc;

				if ($floor == false && $y > max(array_keys($map))) { break 2; } // We've overflowed.
				if (isset($map[$y][$x])) { break 2; } // Space is already Sand

				if (!isset($map[$y + 1][$x]) && (($y + 1) != $floor)) {
					$previousLoc[] = $loc;
					$loc = [$x, $y + 1];
				} else if (!isset($map[$y + 1][$x - 1]) && (($y + 1) != $floor)) {
					$previousLoc[] = $loc;
					$loc = [$x - 1, $y + 1];
				} else if (!isset($map[$y + 1][$x + 1]) && (($y + 1) != $floor)) {
					$previousLoc[] = $loc;
					$loc = [$x + 1, $y + 1];
				} else {
					$map[$y][$x] = 'o';
					$count++;
					break;
				}
			}
		}
		return $count;
	}


	$part1 = addSand($map, $source);
	if (isDebug()) { drawSandMap($map, $source); }
	echo 'Part 1: ', $part1, "\n";

	$floor = max(array_keys($map)) + 2;
	$part2 = addSand($map, $source, $floor, $part1);
	if (isDebug()) { drawSandMap($map, $source, $floor); }
	echo 'Part 2: ', $part2, "\n";
