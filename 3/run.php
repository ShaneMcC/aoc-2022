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
	for ($i = 0; $i < count($rucksacks); $i += 3) {
		$part1 += getPriority(array_values(array_intersect($rucksacks[$i][0], $rucksacks[$i][1]))[0]);
		$part1 += getPriority(array_values(array_intersect($rucksacks[$i + 1][0], $rucksacks[$i + 1][1]))[0]);
		$part1 += getPriority(array_values(array_intersect($rucksacks[$i + 2][0], $rucksacks[$i + 2][1]))[0]);

		$part2 += getPriority(array_values(array_intersect($rucksacks[$i][2], $rucksacks[$i + 1][2], $rucksacks[$i + 2][2]))[0]);
	}
	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
