#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$rucksacks = [];
	$groups = [];
	$group = [];
	foreach ($input as $line) {
		$split = str_split($line, strlen($line)/2);
		$r = [str_split($split[0]), str_split($split[1]), str_split($line)];
		$rucksacks[] = $r;
		$group[] = $r;
		if (count($group) == 3) {
			$groups[] = $group;
			$group = [];
		}
	}
	if (!empty($group)) { $groups[] = $group; }

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

	$part2 = 0;
	foreach ($groups as $g) {
		[$first, $second, $third] = $g;

		foreach ($first[2] as $c) {
			if (in_array($c, $second[2]) && in_array($c, $third[2])) {
				if (strtolower($c) == $c) {
					$p = ord($c) - 96;
				} else {
					$p = ord($c) - 38;
				}

				$part2 += $p;
				break;
			}
		}
	}

	echo 'Part 2: ', $part2, "\n";
