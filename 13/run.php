#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$lines = getInputLines();

	$packets = [];
	foreach ($lines as $line) {
		if (empty($line)) { continue; }
		$packets[] = json_decode($line, true);
	}

	function comparePackets($left, $right) {
		// Ensure we have 2 arrays to look at.
		if (!is_array($left)) { $left = [$left]; }
		if (!is_array($right)) { $right = [$right]; }

		for ($i = 0; $i < max(count($left), count($right)); $i++) {
			if (!isset($left[$i]) && isset($right[$i])) { return -1; } // Left out of items, correct order.
			if (isset($left[$i]) && !isset($right[$i])) { return 1; } // Right out of items, wrong order.

			$l = $left[$i];
			$r = $right[$i];

			if (is_integer($l) && is_integer($r)) {
				if ($l < $r) { return -1; } // Left side is smaller, correct order.
				else if ($l > $r) { return 1; } // Right side is smaller, wrong order.
			} else {
				// Recurse, and return if the numbers not equal
				$compare = comparePackets($l, $r);
				if ($compare !== 0) { return $compare; }
			}
		}

		return 0; // Packets are equal.
	}

	$part1 = 0;
	for ($i = 0; $i < count($packets); $i += 2) {
		$pairNum = (($i+2) / 2);
		if (comparePackets($packets[$i], $packets[$i + 1]) == -1) { $part1 += $pairNum; }
	}
	echo 'Part 1: ', $part1, "\n";

	array_push($packets, [[2]], [[6]]);
	usort($packets, 'comparePackets');
	$part2 = 1;
	foreach ($packets as $k => $p) {
		if ($p == [[2]] || $p == [[6]]) { $part2 *= $k + 1; }
	}
	echo 'Part 2: ', $part2, "\n";
