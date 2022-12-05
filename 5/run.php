#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	[$instacks, $moves] = getInputLineGroups();


	$stacks = [];
	$base = array_pop($instacks);

	foreach ($instacks as $line) {
		$stackId = 1;
		for ($i = 1; $i < strlen($line); $i += 4) {
			$item = trim($line[$i]);
			if (!empty($item)) {
				$stacks[$stackId][] = $item;
			}
			$stackId++;
		}
	}

	foreach ($moves as $line) {
		preg_match('#move (.*) from (.*) to (.*)#SADi', $line, $m);
		[, $count, $from, $to] = $m;

		$removed = array_reverse(array_splice($stacks[$from], 0, $count));
		array_unshift($stacks[$to], ...$removed);
	}

	$part1 = '';
	for ($i = 1; $i <= count($stacks); $i++) {
		$part1 .= $stacks[$i][0];
	}

	echo 'Part 1: ', $part1, "\n";
