#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	for ($i = 3; $i < strlen($input); $i++) {
		$last4 = str_split(substr($input, $i - 3, 4));
		if (count(array_unique($last4)) == 4) {
			$part1 = $i;
			break;
		}
	}

	echo 'Part 1: ', $part1 + 1, "\n";

	for ($i = 13; $i < strlen($input); $i++) {
		$last = str_split(substr($input, $i - 13, 14));
		if (count(array_unique($last)) == 14) {
			$part2 = $i;
			break;
		}
	}

	echo 'Part 2: ', $part2 + 1, "\n";
