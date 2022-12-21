#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$words = [];
	foreach ($input as $line) {
		preg_match('#(.*): (.*)#SADi', $line, $m);
		[, $word, $what] = $m;
		$words[$word] = $what;
	}

	function translate($word) {
		global $words, $__CACHE;

		if (isset($__CACHE[$word])) { return $__CACHE[$word]; }

		$what = $words[$word];
		$answer = NULL;

		if (preg_match('#([0-9]+)#SADi', $what, $m)) {
			$answer = $m[1];
		} else if (preg_match('#([a-z]+) ([/\+\-\*]) ([a-z]+)#SADi', $what, $m)) {
			[, $a, $s, $b] = $m;
			$a = translate($a);
			$b = translate($b);

			if ($s == '/') {
				$answer = $a / $b;
			} else if ($s == '+') {
				$answer = $a + $b;
			} else if ($s == '-') {
				$answer = $a - $b;
			} else if ($s == '*') {
				$answer = $a * $b;
			}
		}

		if ($answer == null) {
			DIE('oops');
		}


		$__CACHE[$word] = $answer;
		return $answer;
	}


	$part1 = translate('root');
	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
