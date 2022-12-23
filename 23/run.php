#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputMap();

	$map = [];
	foreach ($input as $y => $row) {
		foreach ($row as $x => $cell) {
			if ($cell == '#') {
				if (!isset($map[$y])) { $map[$y] = []; }
				$map[$y][$x] = $cell;
			}
		}
	}

	$directions = [ 'N' => [0, -1],
	               'NE' => [1, -1],
	                'E' => [1, 0],
	               'SE' => [1, 1],
	                'S' => [0, 1],
	               'SW' => [-1, 1],
	                'W' => [-1, 0],
	               'NW' => [-1, -1]];

	$proposals = [];
	$proposals[] = [['N', 'NE', 'NW'], 'N'];
	$proposals[] = [['S', 'SE', 'SW'], 'S'];
	$proposals[] = [['W', 'NW', 'SW'], 'W'];
	$proposals[] = [['E', 'NE', 'SE'], 'E'];

	function doRound($map, $proposals) {
		global $directions;

		$proposed = [];
		$attempts = 0;

		foreach ($map as $y => $row) {
			foreach ($row as $x => $cell) {
				if ($cell == '#') {
					$around = [];
					foreach ($directions as $d => $move) {
						[$dX, $dY] = $move;
						$around[$d] = isset($map[$y + $dY][$x + $dX]) ? $map[$y + $dY][$x + $dX] : '.';
					}

					$p = null;
					if (in_array('#', array_values($around))) {
						$attempts++;

						foreach ($proposals as $prop) {
							[[$a, $b, $c], $d] = $prop;
							if ($around[$a] == '.' && $around[$b] == '.' && $around[$c] == '.') {
								$p = $d;
								break;
							}
						}
					}

					if ($p != NULL) {
						$pX = $x + $directions[$p][0];
						$pY = $y + $directions[$p][1];

						if (!isset($proposed[$pY])) { $proposed[$pY] = []; }
						if (!isset($proposed[$pY][$pX])) { $proposed[$pY][$pX] = []; }

						$proposed[$pY][$pX][] = [$x, $y];
					}
				}
			}
		}

		foreach ($proposed as $y => $row) {
			foreach ($row as $x => $elves) {
				if (count($elves) == 1) {
					[$eX, $eY] = $elves[0];

					unset($map[$eY][$eX]);
					if (empty($map[$eY])) { unset($map[$eY]); }
					$map[$y][$x] = '#';
				}
			}
		}

		$p = array_shift($proposals);
		$proposals[] = $p;

		return [$map, $proposals, $attempts];
	}

	if (isDebug()) {
		echo '== Initial State ==', "\n";
		drawSparseMap($map);
		echo "\n";
	}

	$attempts = 0;
	$round = 0;
	do {
		$round++;
		[$map, $proposals, $attempts] = doRound($map, $proposals);
		if (isDebug()) {
			echo '== End of Round ', $round, ' ==', "\n";
			drawSparseMap($map);
			echo "\n";
		}

		if ($round == 10) {
			[$minX, $minY, $maxX, $maxY] = getBoundingBox($map);
			$total = (1 + ($maxX - $minX)) * (1 + ($maxY - $minY));
			$part1 = $total - countCells($map, '#');
			echo 'Part 1: ', $part1, "\n";
		}
	}  while ($attempts != 0);

	$part2 = $round;
	echo 'Part 1: ', $round, "\n";

