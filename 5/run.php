#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	[$instacks, $moves] = getInputLineGroups();

	$base = array_pop($instacks);
	$stacks = array_fill(1, ceil(strlen($base)/4), '');

	foreach ($instacks as $line) {
		for ($stackId = 1; $stackId <= count($stacks); $stackId++) {
			$item = trim($line[(($stackId - 1) * 4) + 1] ?? '');
			if (!empty($item)) {
				$stacks[$stackId] .= $item;
			}
		}
	}

	function moveCrates($stacks, $moves, $part1 = true) {
		foreach ($moves as $line) {
			preg_match('#move (\d+) from (\d+) to (\d+)#Ai', $line, $m);
			[, $count, $from, $to] = $m;

			$removed = substr($stacks[$from], 0, $count);
			$stacks[$from] = substr($stacks[$from], $count);
			if ($part1) { $removed = strrev($removed); }
			$stacks[$to] = $removed . $stacks[$to];
		}

		return $stacks;
	}

	$stacks1 = moveCrates($stacks, $moves);
	$part1 = implode('', array_map(fn($a) => $a[0] ?? '', $stacks1));
	echo 'Part 1: ', $part1, "\n";

	$stacks2 = moveCrates($stacks, $moves, false);
	$part2 = implode('', array_map(fn($a) => $a[0] ?? '', $stacks2));
	echo 'Part 2: ', $part2, "\n";
