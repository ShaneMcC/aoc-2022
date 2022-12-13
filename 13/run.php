#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$lines = getInputLines();

	$packets = [];
	foreach ($lines as $line) {
		if (empty($line)) { continue; }
		$packets[] = json_decode($line, true);
	}

	function comparePairs($left, $right, $level = -1) {
		$debugging = ($level >= 0);
		if ($debugging) { echo str_repeat('  ', $level), '- Compare: ', json_encode($left), ' vs ', json_encode($right), "\n"; }

		if (is_array($left) && !is_array($right)) {
			$right = [$right];
			if ($debugging) {
				echo str_repeat('  ', $level), '- Mixed types; convert right to ', json_encode($right), ' and retry comparison', "\n";
				echo str_repeat('  ', $level), '- Compare: ', json_encode($left), ' vs ', json_encode($right), "\n";
			}
		} else if (!is_array($left) && is_array($right)) {
			$left = [$left];
			if ($debugging) {
				echo str_repeat('  ', $level), '- Mixed types; convert left to ', json_encode($left), ' and retry comparison', "\n";
				echo str_repeat('  ', $level), '- Compare: ', json_encode($left), ' vs ', json_encode($right), "\n";
			}
		}

		for ($i = 0; $i < max(count($left), count($right)); $i++) {
			if (!isset($left[$i]) && isset($right[$i])) {
				if ($debugging) { echo str_repeat('  ', $level + 1), '- Left side ran out of items, so inputs are in the right order', "\n"; }
				return -1;
			}
			if (isset($left[$i]) && !isset($right[$i])) {
				if ($debugging) { echo str_repeat('  ', $level + 1), '- Right side ran out of items, so inputs are not in the right order', "\n"; }
				return 1;
			}

			$l = $left[$i];
			$r = $right[$i];

			if (is_integer($l) && is_integer($r)) {
				if ($debugging) { echo str_repeat('  ', $level + 1), '- Compare: ', json_encode($l), ' vs ', json_encode($r), "\n"; }
				if ($l > $r) {
					if ($debugging) { echo str_repeat('  ', $level + 2), '- Right side is smaller, so inputs are not in the right order', "\n"; }
					return 1;
				} else if ($l < $r) {
					if ($debugging) { echo str_repeat('  ', $level + 2), '- Left side is smaller, so inputs are in the right order', "\n"; }
					return -1;
				}
			} else {
				$compare = comparePairs($l, $r, ($debugging ? $level + 1 : -1));
				if ($compare !== 0) { return $compare; }
			}
		}

		return 0;
	}

	$part1 = 0;
	for ($i = 0; $i < count($packets); $i += 2) {
		$pairNum = (($i+2) / 2);
		if (isDebug()) { echo '== Pair ', $pairNum, ' ==', "\n"; }
		if (comparePairs($packets[$i], $packets[$i + 1], (isDebug() ? 0 : -1)) == -1) { $part1 += $pairNum; }
		if (isDebug()) { echo "\n"; }
	}

	echo 'Part 1: ', $part1, "\n";

	array_push($packets, [[2]], [[6]]);

	usort($packets, 'comparePairs');
	$part2 = 1;
	foreach ($packets as $k => $p) {
		if ($p == [[2]] || $p == [[6]]) { $part2 *= $k + 1; }
	}
	echo 'Part 2: ', $part2, "\n";
