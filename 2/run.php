#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$theirs = ['A' => 'Rock', 'B' => 'Paper', 'C' => 'Scissors'];
	$ours = ['X' => 'Rock', 'Y' => 'Paper', 'Z' => 'Scissors'];

	$entries = [];
	foreach ($input as $line) {
		preg_match('#(.*) (.*)#SADi', $line, $m);
		[$all, $them, $us] = $m;
		$entries[] = [$theirs[$them], $ours[$us]];
	}


	$shapeValue = ['Rock' => 1, 'Paper' => 2, 'Scissors' => 3];
	$resultValue = ['Loss' => 0, 'Draw' => 3, 'Win' => 6];
	$winning = ['Rock' => 'Scissors', 'Paper' => 'Rock', 'Scissors' => 'Paper'];


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


	$part1 = $score;
	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
