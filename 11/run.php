#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	$monkies = [];
	foreach ($input as $group) {
		$monkey = [];
		foreach ($group as $line) {
			if (preg_match('#Monkey ([0-9]+):#', $line, $m)) {
				$monkey['id'] = $m[1];
				$monkey['inspectCount'] = 0;
			} else if (preg_match('#Starting items: (.*)$#', $line, $m)) {
				$monkey['items'] = explode(',', str_replace(' ', '', $m[1]));
			} else if (preg_match('#Operation: new = old (.*) (.*)$#', $line, $m)) {
				if ($m[1] == '*' && $m[2] == 'old') { $monkey['operation'] = fn($x) => $x * $x; }
				else if ($m[1] == '*' && is_numeric($m[2])) { $monkey['operation'] = fn($x) => $x * $m[2]; }
				else if ($m[1] == '+' && is_numeric($m[2])) { $monkey['operation'] = fn($x) => $x + $m[2]; }
				else { die('Unknown operation: ' . $line); }
			} else if (preg_match('#Test: divisible by (.*)$#', $line, $m)) {
				$monkey['test'] = ['condition' => $m[1]];
			} else if (preg_match('#If (true|false): throw to monkey (.*)$#', $line, $m)) {
				$monkey['test'][$m[1] == 'true'] = $m[2];
			} else {
				die('Unknown line: ' . $line);
			}
		}

		$monkies[$monkey['id']] = $monkey;
	}
	$allDivisors = array_reduce($monkies, fn($c, $m) => lcm($m['test']['condition'], $c), 1);

	function processRound(&$monkies, $worryReduction = null, $debugging = null) {
		$debugging ??= isDebug();

		foreach ($monkies as &$monkey) {
			if ($debugging) { echo 'Monkey ', $monkey['id'], ':', "\n"; }

			foreach ($monkey['items'] as $item) {
				$monkey['inspectCount']++;
				if ($debugging) { echo "\t", 'Monkey inspects an item with a worry level of ', $item, "\n"; }

				$item = $monkey['operation']($item);
				if ($debugging) { echo "\t\t", 'Worry level is now: ', $item, "\n"; }

				$item = $worryReduction($item);
				if ($debugging) { echo "\t\t", 'Worry level reduction, item is now ', $item, "\n"; }

				$result = ($item % $monkey['test']['condition'] == 0);
				$newMonkey = $monkey['test'][$result];
				$monkies[$newMonkey]['items'][] = $item;

				if ($debugging) {
					echo "\t\t", 'Current worry level ', ($result ? 'is' : 'is not'), ' divisible by: ', $monkey['test']['condition'], "\n";
					echo "\t\t", 'Item with worry level ', $item, ' is thrown to ', $newMonkey, "\n";
				}
			}
			$monkey['items'] = [];
		}
	}

	$startingMonkies = $monkies;
	$worryReduction = fn($item) => floor($item / 3);
	for ($i = 0; $i < 20; $i++) {
		if (isDebug()) { echo '==========', "\n", 'Part 1 - Round ', $i, "\n", '==========', "\n"; }
		processRound($monkies, $worryReduction);
	}
	if (isDebug()) {
		foreach ($monkies as $m) {
			echo 'Monkey ', $m['id'], ': ', $m['inspectCount'], "\n";
		}
	}

	$part1 = array_product(array_slice(sorted('rsort', array_map(fn($m) => $m['inspectCount'], $monkies)), 0, 2));
	echo 'Part 1: ', $part1, "\n";

	$monkies = $startingMonkies;
	$allDivisors = array_product(array_map(fn($m) => $m['test']['condition'], $monkies));
	$worryReduction = fn($item) => $item % $allDivisors;

	for ($i = 0; $i < 10000; $i++) {
		if (isDebug() && $i < 20) { echo '==========', "\n", 'Part 2 - Round ', $i, "\n", '==========', "\n"; }
		processRound($monkies, $worryReduction, (isDebug() && $i < 20));

		if (isDebug() && $i == 19) {
			echo '==========', "\n";
			foreach ($monkies as $m) {
				echo 'Monkey ', $m['id'], ': ', $m['inspectCount'], "\n";
			}
			echo '==========', "\n";
			echo '...', "\n";
		}
	}
	if (isDebug()) {
		foreach ($monkies as $m) {
			echo 'Monkey ', $m['id'], ': ', $m['inspectCount'], "\n";
		}
	}

	$part2 = array_product(array_slice(sorted('rsort', array_map(fn($m) => $m['inspectCount'], $monkies)), 0, 2));
	echo 'Part 2: ', $part2, "\n";
