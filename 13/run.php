#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$groups = getInputLineGroups();

	$packets = [[[2]], [[6]]];
	$pairs = [];
	foreach ($groups as $group) {
		$pair = [];
		foreach ($group as $g) {
			$p = json_decode($g, true);
			$pair[] = $p;
			$packets[] = $p;
		}
		$pairs[] = $pair;
	}

	function comparePairs($first, $second, $level = 0) {
		if (isDebug()) { echo str_repeat("\t", $level), 'Compare: ', json_encode($first), ' vs ', json_encode($second), "\n"; }

		for ($i = 0; $i < max(count($first), count($second)); $i++) {
			if (!isset($first[$i]) && isset($second[$i])) {
				if (isDebug()) { echo str_repeat("\t", $level), 'No more left.', "\n"; }
				return -1;
			}
			if (isset($first[$i]) && !isset($second[$i])) {
				if (isDebug()) { echo str_repeat("\t", $level), 'No more right.', "\n"; }
				return 1;
			}

			$left = $first[$i];
			$right = $second[$i];

			if (is_integer($left) && is_integer($right)) {
				if (isDebug()) { echo str_repeat("\t", $level), "\t\t", 'int: ', json_encode($left), ' with ', json_encode($right), "\n"; }
				if ($left > $right) {
					return 1;
				} else if ($left < $right) {
					return -1;
				}
			} else if (is_array($left) || is_array($right)) {
				$left = is_array($left) ? $left : [$left];
				$right = is_array($right) ? $right : [$right];

				$compare = comparePairs($left, $right, $level + 1);
				if ($compare !== 0) { return $compare; }
			}
		}

		return 0;
	}

	$part1 = 0;
	$p = 1;
	for ($p = 1; $p <= count($pairs); $p++) {
		if (isDebug()) {
			echo '==========', "\n";
			echo 'Pair ', $p, "\n";
			echo '==========', "\n";
		}
		[$first, $second] = $pairs[$p - 1];
		$correct = comparePairs($first, $second) == -1;

		if (isDebug()) { echo '==> ', json_encode($correct), "\n"; }
		if ($correct) { $part1 += $p; }
	}

	echo 'Part 1: ', $part1, "\n";

	usort($packets, 'comparePairs');

	$part2 = 1;
	foreach ($packets as $k => $p) {
		if ($p == [[2]] || $p == [[6]]) { $part2 *= $k + 1; }
	}

	echo 'Part 2: ', $part2, "\n";
