#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$instructions = [[]];
	foreach ($input as $line) {
		$instructions[] = explode(' ', $line);
	}

	function processInstructions($instructions) {
		$x = 1;
		$xSum = 0;

		$startTime = 2;
		$pending = [];
		$screen = '';
		for ($i = 1; $i <= 240; $i++) {
			if ($i == 20 || $i == 60 || $i == 100 || $i ==  140 || $i ==  180 || $i ==  220) {
				if (isDebug()) { echo 'Cycle ', $i, ' x is: ', $x, "\n"; }
				$xSum += ($i * $x);
			}

			if (isset($pending[$i])) {
				if ($pending[$i][0] == 'addx') {
					if (isDebug()) { echo $i, ' Adding: ', $pending[$i][1], "\n"; }
					$x += $pending[$i][1];
				}
			}

			$screen .= $x == ($i % 40) -1 || $x == ($i % 40) || $x == ($i % 40) + 1 ? '#' : '.';

			$next = $instructions[$i] ?? ['noop'];
			$pending[$startTime] = $next;

			if ($next[0] == 'noop') {
				$startTime += 1;
			} else if ($next[0] == 'addx') {
				$startTime += 2;
			}
		}

		return [$xSum, str_split($screen, 40)];
	}

	[$part1, $screen] = processInstructions($instructions);

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', "\n";
	echo implode("\n", $screen);
