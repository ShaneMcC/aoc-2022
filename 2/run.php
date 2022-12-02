#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$options = ['Rock' => ['defeats' => 'Scissors', 'defeated' => 'Paper', 'value' => 1],
	            'Paper' => ['defeats' => 'Rock', 'defeated' => 'Scissors', 'value' => 2],
	            'Scissors' => ['defeats' => 'Paper', 'defeated' => 'Rock', 'value' => 3],
	           ];

	$theirKey = ['A' => 'Rock', 'B' => 'Paper', 'C' => 'Scissors'];
	$ourKey = ['X' => 'Rock', 'Y' => 'Paper', 'Z' => 'Scissors'];

	$strategy1 = $strategy2 = [];
	foreach ($input as $line) {
		preg_match('#(.*) (.*)#SADi', $line, $m);
		[, $them, $us] = $m;

		$wantedResult = $us;

		$them = $theirKey[$them];
		$us = $ourKey[$us];

		$strategy1[] = [$them, $us];

		if ($wantedResult == 'X') { // Lose
			$us = $options[$them]['defeats']; // What do they win against
		} else if ($wantedResult == 'Y') { // Draw
			$us = $them; // Same as them
		} else if ($wantedResult == 'Z') { // Win
			$us = $options[$them]['defeated'];  // What do they lose against
		}
		$strategy2[] = [$them, $us];
	}

	function getScore($strategy, $options) {
		$score = 0;
		foreach ($strategy as $game) {
			[$them, $us] = $game;

			if ($them == $us) {
				$score += 3;
			} else if ($options[$us]['defeats'] == $them) {
				$score += 6;
			}

			$score += $options[$us]['value'];
		}

		return $score;
	}


	$part1 = getScore($strategy1, $options);
	echo 'Part 1: ', $part1, "\n";

	$part2 = getScore($strategy2, $options);
	echo 'Part 2: ', $part2, "\n";
