#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$blueprints = [];
	foreach ($input as $line) {
		preg_match('#Blueprint (.*): Each ore robot costs (.*) ore. Each clay robot costs (.*) ore. Each obsidian robot costs (.*) ore and (.*) clay. Each geode robot costs (.*) ore and (.*) obsidian.#SADi', $line, $m);
		[, $bp, $oreOre, $clayOre, $obsidianOre, $obsidianClay, $geodeOre, $geodeObsidian] = $m;
		$blueprints[] = ['num' => $bp, 'costs' => ['ore' => ['ore' => $oreOre], 'clay' => ['ore' => $clayOre], 'obsidian' => ['ore' => $obsidianOre, 'clay' => $obsidianClay], 'geode' => ['ore' => $geodeOre, 'obsidian' => $geodeObsidian]], 'result' => []];
	}

	function buildBP($bp) {
		$result = ['quality' => -1];

		$m = 1;
		$materials = ['ore' => 0, 'clay' => 0, 'obsidian' => 0, 'geode' => 0];
		$robots = ['ore' => 1, 'clay' => 0, 'obsidian' => 0, 'geode' => 0];
		$state = [$m, $materials, $robots];

		$queue = new SPLPriorityQueue();
		$queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
		$queue->insert($state, 0);

		while (!$queue->isEmpty()) {
			$q = $queue->extract();
			[$m, $materials, $robots] = $q['data'];
			$quality = $materials['geode'] * $bp['num'];

			if ($m == 24) {
				// Store this option if it is a better result than we had
				// before.
				if ($quality > $result['quality']) {
					$result = ['materials' => $materials, 'robots' => $robots, 'quality' => $quality];
				}
				continue;
			}

			$options = [];

			// Check what options we can build.
			foreach (['geode', 'obsidian', 'clay', 'ore'] as $robotType) {
				// Always build geode robots.
				$canBuild = ($robotType == 'geode');

				// If we do not benefit from building this type
				// then do not.
				foreach ($bp['costs'] as $c) {
					// If this blueprint needs more of a robot type than we
					// have, then we can consider building it.
					if (isset($c[$robotType]) && $c[$robotType] > $robots[$robotType]) {
						$canBuild = true;
					}
				}

				// Check that we have enough current materials to build this
				// robot type.
				foreach ($bp['costs'][$robotType] as $type => $amount) {
					if ($materials[$type] < $amount) {
						$canBuild = false;
					}
				}

				// If this is a robot type we want to build, then add it as
				// a build option to our queue.
				if ($canBuild) {
					$o = ['build' => $robotType, 'materials' => $materials, 'robots' => $robots];
					foreach ($bp['costs'][$robotType] as $type => $amount) {
						$o['materials'][$type] -= $amount;
					}
					$options[] = $o;
				}
			}
			$options[] = ['build' => null, 'materials' => $materials, 'robots' => $robots];

			// Deal with the various options.
			foreach ($options as $o) {
				// All options harvest some material
				foreach ($robots as $type => $amount) {
					$o['materials'][$type] += $amount;
				}

				// Finish any applicable build.
				if ($o['build'] != null) {
					$o['robots'][$o['build']]++;
				}

				// Queue this option for further investigation
				$queue->insert([($m + 1), $o['materials'], $o['robots']], -($m));
			}
		}

		return $result;
	}

	foreach ($blueprints as $bpid => $bp) {
		$blueprints[$bpid]['result'] = buildBP($bp);
		var_dump($blueprints[$bpid]['result']);
		die();
	}

//	var_dump($blueprints);

	$part1 = array_sum(array_map(fn($a) => $a['result']['quality'], $blueprints));
	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
