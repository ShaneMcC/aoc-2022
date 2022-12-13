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
		if (is_integer($left) && is_integer($right)) {
			return $left <=> $right;
		}

		if (!is_array($left)) { $left = [$left]; }
		if (!is_array($right)) { $right = [$right]; }

		for ($i = 0; $i < min(count($left), count($right)); $i++) {
			$compare = comparePackets($left[$i], $right[$i]);
			if ($compare !== 0) { return $compare; }
		}

		return count($left) <=> count($right);
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
