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

		$common = array_intersect($first, $second);
		$common = array_shift($common);
		$part1 += getPriority($common);

		if ($i % 3 == 0) {
			$common = array_intersect($rucksacks[$i][2], $rucksacks[$i + 1][2], $rucksacks[$i + 2][2]);
			$common = array_shift($common);
			$part2 += getPriority($common);
		}
	}
	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
