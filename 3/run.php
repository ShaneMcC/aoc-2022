#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$rucksacks = [];
	foreach ($input as $line) {
		$split = str_split($line, strlen($line)/2);
		$rucksacks[] = [str_split($split[0]), str_split($split[1])];
	}

	$part1 = 0;
	foreach ($rucksacks as $r) {
		[$first, $second] = $r;

		foreach ($first as $c) {
			if (in_array($c, $second)) {
				if (strtolower($c) == $c) {
					$p = ord($c) - 96;
				} else {
					$p = ord($c) - 38;
				}

				$part1 += $p;
				break;
			}
		}
	}

	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
