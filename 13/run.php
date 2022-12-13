#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$groups = getInputLineGroups();

	$pairs = [];
	foreach ($groups as $group) {
		$pair = [];
		foreach ($group as $g) {
			$pair[] = json_decode($g, true);
		}
		$pairs[] = $pair;
	}

	function comparePairs($first, $second, $level = 0) {
		if (isDebug()) { echo str_repeat("\t", $level), 'Compare: ', json_encode($first), ' vs ', json_encode($second), "\n"; }

		for ($i = 0; $i < max(count($first), count($second)); $i++) {
			if (!isset($first[$i]) && isset($second[$i])) {
				if (isDebug()) { echo str_repeat("\t", $level), 'No more left.', "\n"; }
				return true;
			}
			if (isset($first[$i]) && !isset($second[$i])) {
				if (isDebug()) { echo str_repeat("\t", $level), 'No more right.', "\n"; }
				return false;
			}

			$left = $first[$i];
			$right = $second[$i];

			if (is_integer($left) && is_integer($right)) {
				if (isDebug()) { echo str_repeat("\t", $level), "\t\t", 'int: ', json_encode($left), ' with ', json_encode($right), "\n"; }
				if ($left > $right) {
					return false;
				} else if ($left < $right) {
					return true;
				}
			} else if (is_array($left) || is_array($right)) {
				$left = is_array($left) ? $left : [$left];
				$right = is_array($right) ? $right : [$right];

				$compare = comparePairs($left, $right, $level + 1);
				if ($compare !== null) { return $compare; }
			}
		}

		return null;
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
		$correct = comparePairs($first, $second);

		if (isDebug()) { echo '==> ', json_encode($correct), "\n"; }
		if ($correct) { $part1 += $p; }
	}

	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
