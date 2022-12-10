#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	require_once(dirname(__FILE__) . '/../common/decodeText.php');
	$input = getInputLines();

	$handlers = [];
	$handlers['noop'] = ['ticks' => 1, 'handle' => fn($x, $instr) => $x];
	$handlers['addx'] = ['ticks' => 2, 'handle' => fn($x, $instr) => $x += $instr[1]];

	$instructions = [['']];
	foreach ($input as $line) {
		$instructions[] = explode(' ', $line);
	}

	function processInstructions($instructions) {
		global $handlers;

		$x = 1;
		$xSum = 0;

		$startTime = 0;
		$deferred = [];
		$screen = '';
		for ($i = 0; $i < 240; $i++) {
			// Part 1 wants the sum at the start of the tick
			if (($i - 20) % 40 == 0) {
				$xSum += ($i * $x);
			}

			// Find the current instruction, and then put it into our
			// deferred array for later processing after the correct amount of
			// ticks have passed.
			$next = $instructions[$i] ?? ['noop'];
			if (isset($handlers[$next[0]]['ticks'])) { $startTime += $handlers[$next[0]]['ticks']; }
			if (isset($handlers[$next[0]]['handle'])) { $deferred[$startTime] = $next; }

			// Process any deferred instructions at the end of the tick.
			if (isset($deferred[$i])) {
				$instr = $deferred[$i];
				$x = $handlers[$instr[0]]['handle']($x, $instr);
			}

			// Draw the screen sprites.
			$screen .= $x == ($i % 40) -1 || $x == ($i % 40) || $x == ($i % 40) + 1 ? '#' : ' ';
		}

		return [$xSum, str_split($screen, 40)];
	}

	[$part1, $screen] = processInstructions($instructions);

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', decodeText($screen), "\n";
	if (isDebug()) {
		echo "\n";
		foreach ($screen as $line) { echo str_replace('#', '█', $line), "\n"; }
		echo "\n";
	}
