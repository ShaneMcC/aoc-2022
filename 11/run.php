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
				switch ($m[1]) {
					case '*':
						$monkey['operation'] = fn($x) => $x * ($m[2] == 'old' ? $x : $m[2]);
						break;
					case '-':
						$monkey['operation'] = fn($x) => $x - ($m[2] == 'old' ? $x : $m[2]);
						break;
					case '+':
						$monkey['operation'] = fn($x) => $x + ($m[2] == 'old' ? $x : $m[2]);
						break;
					default:
						die('Unknown operation: ' . $line);
				}
			} else if (preg_match('#Test: divisible by (.*)$#', $line, $m)) {
				$monkey['test'] = [];
				$monkey['test']['condition'] = $m[1];
			} else if (preg_match('#If true: throw to monkey (.*)$#', $line, $m)) {
				$monkey['test'][true] = $m[1];
			} else if (preg_match('#If false: throw to monkey (.*)$#', $line, $m)) {
				$monkey['test'][false] = $m[1];
			} else {
				die('Unknown line: ' . $line);
			}
		}

		$monkies[$monkey['id']] = $monkey;
	}


	function processRound(&$monkies) {
		foreach (array_keys($monkies) as $mid) {
			$monkey = &$monkies[$mid];

			echo 'Monkey ', $mid, ':', "\n";

			foreach ($monkey['items'] as $item) {
				if (isDebug()) { echo "\t", 'Monkey inspects an item with a worry level of ', $item, "\n"; }
				$item = $monkey['operation']($item);
				if (isDebug()) { echo "\t\t", 'Worry level is now: ', $item, "\n"; }
				$item = floor($item / 3);
				if (isDebug()) { echo "\t\t", 'Monkey gets bored with item. Worry level is divided by 3 to ', $item, "\n"; }

				$result = ($item % $monkey['test']['condition'] == 0);
				$newMonkey = $monkey['test'][$result];

				if (isDebug()) {
					echo "\t\t", 'Current worry level ', ($result ? 'is' : 'is not'), ' divisible by: ', $monkey['test']['condition'], "\n";
					echo "\t\t", 'Item with worry level ', $item, ' is thrown to ', $newMonkey, "\n";
				}

				$monkies[$newMonkey]['items'][] = $item;
				$monkey['inspectCount']++;
			}
			$monkey['items'] = [];
		}
	}

	$startingMonkies = $monkies;

	for ($i = 0; $i < 20; $i++) {
		processRound($monkies);
	}
	if (isDebug()) {
		foreach ($monkies as $m) {
			echo 'Monkey ', $m['id'], ': ', $m['inspectCount'], "\n";
		}
	}

	$part1 = array_map(fn($m) => $m['inspectCount'], $monkies);
	rsort($part1);

	$part1 = $part1[0] * $part1[1];
	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
