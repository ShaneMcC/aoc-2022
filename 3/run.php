#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$rucksacks = [];
	foreach ($input as $line) {
		$split = str_split($line, strlen($line)/2);
		$r = [str_split($split[0]), str_split($split[1]), str_split($line)];
		$rucksacks[] = $r;
	}

	function getPriority($c) {
		return ord($c) - (strtolower($c) == $c ? 96 : 38);
	}


	$part1 = $part2 = 0;
	for ($i = 0; $i < count($rucksacks); $i++) {
		[$first, $second] = $rucksacks[$i];

		foreach ($first as $c) {
			if (in_array($c, $second)) {
				$part1 += getPriority($c);
				break;
			}
		}

		if ($i % 3 == 0) {
			foreach ($rucksacks[$i][2] as $c) {
				if (in_array($c, $rucksacks[$i + 1][2]) && in_array($c, $rucksacks[$i + 2][2])) {
					$part2 += getPriority($c);
					break;
				}
			}
		}
	}
	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
