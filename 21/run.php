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

	function translate($word, $part2 = false) {
		global $words, $__CACHE;

		if ($part2 && $word == 'humn') { throw new Exception('Human Needed'); }

		$cacheKey = ($part2 ? '2' : '1') . ',' . $word;
		if (isset($__CACHE[$cacheKey])) { return $__CACHE[$cacheKey]; }

		$what = $words[$word];
		$answer = NULL;

		if (preg_match('#([0-9]+)#SADi', $what, $m)) {
			$answer = $m[1];
		} else if (preg_match('#([a-z]+) ([/\+\-\*]) ([a-z]+)#SADi', $what, $m)) {
			$a = translate($m[1], $part2);
			$op = $m[2];
			$b = translate($m[3], $part2);

			if ($op == '+') { $answer = $a + $b; }
			else if ($op == '-') { $answer = $a - $b; }
			else if ($op == '*') { $answer = $a * $b; }
			else if ($op == '/') { $answer = $a / $b; }
		}

		$__CACHE[$cacheKey] = $answer;
		return $answer;
	}


	$part1 = translate('root');
	echo 'Part 1: ', $part1, "\n";

	function needsHuman($word) {
		global $words;

		$splitWords = explode(' ', $words[$word]);
		$op = $splitWords[1];

		try {
			$human = $splitWords[2];
			$value = translate($splitWords[0], true);
			$leftIsHuman = true;
		} catch (Exception $e) {
			$human = $splitWords[0];
			$value = translate($splitWords[2], true);
			$leftIsHuman = false;
		}

		return [$human, $op, $value, $leftIsHuman];
	}

	[$check, , $target] = needsHuman('root');

	while ($check != 'humn') {
		[$check, $op, $value, $leftIsHuman] = needsHuman($check);

		// * and + are always just inverted.
		// / and - differ depending on if the human is left or right.
		//
		// If the human is on the left, we do the regular operation.
		// If the human is on the right, we do the inverse operation.

		if ($op == '+') { $target = $target - $value; }
		else if ($op == '*') { $target = $target / $value; }
		else if ($op == '-') { $target = $leftIsHuman ? ($value - $target) : ($target + $value); }
		else if ($op == '/') { $target = $leftIsHuman ? ($value / $target) : ($target * $value); }
	}

	$part2 = $target;
	echo 'Part 2: ', $target, "\n";
