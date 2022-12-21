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

	// Translate a word from the word list.
	// If part2 is try we will throw an exception if we need a human.
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

	// Partial translate.
	// Returns a word that we can't translate
	// the operation we want to do
	// the value we can translate
	// and a boolean if the untranslatable word was the left operand
	function partialTranslate($word) {
		global $words;

		$splitWords = explode(' ', $words[$word]);
		$op = $splitWords[1];

		try {
			$human = $splitWords[2];
			$value = translate($splitWords[0], true);
			$leftIsHuman = false;
		} catch (Exception $e) {
			$human = $splitWords[0];
			$value = translate($splitWords[2], true);
			$leftIsHuman = true;
		}

		return [$human, $op, $value, $leftIsHuman];
	}

	// Part 1 is easy, just translate stuff.
	// translate() will recursively call itself as needed.
	$part1 = translate('root');
	echo 'Part 1: ', $part1, "\n";

	// For part 2, we can partially translate half of the thing
	// The other half we need to figure out what to say to make it work
	//
	// So we will take the target, then look at each word we can't translate
	// They will all also partially-translate, so we will then be able to
	// apply the mathematical operations against the target number in reverse
	// to get all the way up to the human value we need to shout.
	[$check, , $target] = partialTranslate('root');
	while ($check != 'humn') {
		[$check, $op, $value, $leftIsHuman] = partialTranslate($check);

		// * and + are always just inverse operation against the target value.
		//
		//  / and - differ depending on if the human is left or right.
		//
		// If the human is on the left, we do the inverse operation against the target
		// If the human is on the right, we do the forward operation with the target

		if ($op == '+') { $target = $target - $value; }
		else if ($op == '*') { $target = $target / $value; }
		else if ($op == '-') { $target = $leftIsHuman ? ($target + $value) : ($value - $target); }
		else if ($op == '/') { $target = $leftIsHuman ? ($target * $value) : ($value / $target); }
	}

	$part2 = $target;
	echo 'Part 2: ', $target, "\n";
