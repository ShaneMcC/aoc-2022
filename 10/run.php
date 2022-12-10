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

		$startTime = 2;
		$deferred = [];
		$screen = '';
		for ($i = 0; $i < 240; $i++) {
			if (($i - 20) % 40 == 0) {
				$xSum += ($i * $x);
			}

			if (isset($deferred[$i])) {
				$instr = $deferred[$i];
				$x = $handlers[$instr[0]]['handle']($x, $instr);
			}

			$screen .= $x == ($i % 40) -1 || $x == ($i % 40) || $x == ($i % 40) + 1 ? '#' : ' ';

			$next = $instructions[$i] ?? ['noop'];

			if (isset($handlers[$next[0]]['handle'])) { $deferred[$startTime] = $next; }
			if (isset($handlers[$next[0]]['ticks'])) { $startTime += $handlers[$next[0]]['ticks']; }
		}

		return [$xSum, str_split($screen, 40)];
	}

	[$part1, $screen] = processInstructions($instructions);

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', decodeText($screen), "\n";
	if (isDebug()) {
		echo implode("\n", $screen), "\n";
	}
