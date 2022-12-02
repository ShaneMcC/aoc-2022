#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$theirs = ['A' => 'Rock', 'B' => 'Paper', 'C' => 'Scissors'];
	$ours = ['X' => 'Rock', 'Y' => 'Paper', 'Z' => 'Scissors'];

	$shapeValue = ['Rock' => 1, 'Paper' => 2, 'Scissors' => 3];
	$resultValue = ['Loss' => 0, 'Draw' => 3, 'Win' => 6];
	$winning = ['Rock' => 'Scissors', 'Paper' => 'Rock', 'Scissors' => 'Paper'];
	$losing = ['Scissors' => 'Rock', 'Rock' => 'Paper', 'Paper' => 'Scissors'];

	$entries1 = [];
	$entries2 = [];
	foreach ($input as $line) {
		preg_match('#(.*) (.*)#SADi', $line, $m);
		[$all, $them, $us] = $m;

		$entries1[] = [$theirs[$them], $ours[$us]];

		if ($us == 'X') {
			$us = $winning[$theirs[$them]];
		} else if ($us == 'Y') {
			$us = $theirs[$them];
		} else if ($us == 'Z') {
			$us = $losing[$theirs[$them]];
		}
		$entries2[] = [$theirs[$them], $us];
	}

	function getScore($entries) {
		global $shapeValue, $resultValue, $winning;

		$score = 0;
		foreach ($entries as $game) {
			[$them, $us] = $game;

			if ($them == $us) {
				$score += $resultValue['Draw'];
			} else if ($winning[$us] == $them) {
				$score += $resultValue['Win'];
			} else {
				$score += $resultValue['Loss'];
			}

			$score += $shapeValue[$us];
		}

		return $score;
	}


	$part1 = getScore($entries1);
	echo 'Part 1: ', $part1, "\n";

	$part2 = getScore($entries2);
	echo 'Part 2: ', $part2, "\n";
