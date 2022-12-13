#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$lines = getInputLines();

	$packets = [];
	foreach ($lines as $line) {
		if (empty($line)) { continue; }
		$packets[] = json_decode($line, true);
	}

	function comparePackets($left, $right, $level = -1) {
		$debugging = ($level >= 0);
		if ($debugging) { echo str_repeat('  ', $level), '- Compare: ', json_encode($left), ' vs ', json_encode($right), "\n"; }

		// Ensure we have 2 arrays to look at.
		if (!is_array($left) && is_array($right)) {
			$left = [$left];
			if ($debugging) {
				echo str_repeat('  ', $level), '- Mixed types; convert left to ', json_encode($left), ' and retry comparison', "\n";
				echo str_repeat('  ', $level), '- Compare: ', json_encode($left), ' vs ', json_encode($right), "\n";
			}
		} else if (!is_array($right) && is_array($left)) {
			$right = [$right];
			if ($debugging) {
				echo str_repeat('  ', $level), '- Mixed types; convert right to ', json_encode($right), ' and retry comparison', "\n";
				echo str_repeat('  ', $level), '- Compare: ', json_encode($left), ' vs ', json_encode($right), "\n";
			}
		}

		for ($i = 0; $i < max(count($left), count($right)); $i++) {
			if (!isset($left[$i]) && isset($right[$i])) {
				if ($debugging) { echo str_repeat('  ', $level + 1), '- Left side ran out of items, so inputs are in the right order', "\n"; }
				return -1; // Left out of items, correct order.
			}
			if (isset($left[$i]) && !isset($right[$i])) {
				if ($debugging) { echo str_repeat('  ', $level + 1), '- Right side ran out of items, so inputs are not in the right order', "\n"; }
				return 1;  // Right out of items, wrong order.
			}

			$l = $left[$i];
			$r = $right[$i];

			if (is_integer($l) && is_integer($r)) {
				if ($debugging) { echo str_repeat('  ', $level + 1), '- Compare: ', json_encode($l), ' vs ', json_encode($r), "\n"; }
				if ($l < $r) {
					if ($debugging) { echo str_repeat('  ', $level + 2), '- Left side is smaller, so inputs are in the right order', "\n"; }
					return -1; // Left side is smaller, correct order.
				} else if ($l > $r) {
					if ($debugging) { echo str_repeat('  ', $level + 2), '- Right side is smaller, so inputs are not in the right order', "\n"; }
					return 1; // Right side is smaller, wrong order.
				}
			} else {
				// Recurse, and return if the numbers not equal
				$compare = comparePackets($l, $r, ($debugging ? $level + 1 : -1));
				if ($compare !== 0) { return $compare; }
			}
		}

		return 0; // Packets are equal.
	}

	$part1 = 0;
	for ($i = 0; $i < count($packets); $i += 2) {
		$pairNum = (($i+2) / 2);
		if (isDebug()) { echo '== Pair ', $pairNum, ' ==', "\n"; }
		if (comparePackets($packets[$i], $packets[$i + 1], (isDebug() ? 0 : -1)) == -1) { $part1 += $pairNum; }
		if (isDebug()) { echo "\n"; }
	}

	echo 'Part 1: ', $part1, "\n";

	array_push($packets, [[2]], [[6]]);
	usort($packets, 'comparePackets');
	$part2 = 1;
	foreach ($packets as $k => $p) {
		if ($p == [[2]] || $p == [[6]]) { $part2 *= $k + 1; }
	}
	echo 'Part 2: ', $part2, "\n";
