#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$blueprints = [];
	foreach ($input as $line) {
		preg_match('#Blueprint (.*): Each ore robot costs (.*) ore. Each clay robot costs (.*) ore. Each obsidian robot costs (.*) ore and (.*) clay. Each geode robot costs (.*) ore and (.*) obsidian.#SADi', $line, $m);

		[, $bp, $oreOre, $clayOre, $obsidianOre, $obsidianClay, $geodeOre, $geodeObsidian] = $m;

		$blueprints[] = ['num' => $bp,
		                 'costs' => ['ore' => ['ore' => $oreOre],
		                             'clay' => ['ore' => $clayOre],
		                             'obsidian' => ['ore' => $obsidianOre, 'clay' => $obsidianClay],
		                             'geode' => ['ore' => $geodeOre, 'obsidian' => $geodeObsidian]
		                            ],
		                 'quality' => []
		                ];
	}

	function getMaxRobots($bp) {
		$required = [];
		foreach ($bp['costs'] as $robotType) {
			foreach ($robotType as $type => $cost) {
				$required[$type] = max($required[$type] ?? 0, $cost);
			}
		}

		$required['geode'] = PHP_INT_MAX;

		return $required;
	}

	function canBuild($bp, $robotType, $materials) {
		foreach ($bp['costs'][$robotType] as $type => $amount) {
			if ($materials[$type] < $amount) {
				return false;
			}
		}

		return true;
	}

	function getBuildOptions($bp, $materials, $robots) {
		$options = [];

		$maxNeeded = getMaxRobots($bp);

		// If we can build a robot, and we have the materials to do it.
		// Consider it.
		$preference = ['geode' => true, 'obsidian' => true, 'clay' => false, 'ore' => false];
		foreach ($preference as $robotType => $onlyConsiderThis) {
			if (canBuild($bp, $robotType, $materials) && $robots[$robotType] < $maxNeeded[$robotType]) {
				if ($onlyConsiderThis) {
					return [$robotType];
				} else {
					$options[] = $robotType;
				}
			}
		}

		// Build nothing, just harvest.
		if ($materials['ore'] <= $maxNeeded['ore']) {
			$options[] = null;
		}

		return $options;
	}

	function buildBP($bp, $maxTime) {
		$geodeCount = -1;

		$materials = ['ore' => 0, 'clay' => 0, 'obsidian' => 0, 'geode' => 0];
		$robots = ['ore' => 1, 'clay' => 0, 'obsidian' => 0, 'geode' => 0];
		$state = [1, $materials, $robots];

		$queue = new SPLPriorityQueue();
		$queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
		$queue->insert($state, 0);

		$visted = [];

		while (!$queue->isEmpty()) {
			$q = $queue->extract();
			[$time, $materials, $robots] = $q['data'];

			// Store this option if it is a better result than we had
			// before.
			$geodeCount = max($geodeCount, $materials['geode']);

			// Don't go past max time.
			if ($time > $maxTime) { continue; }

			// If we have seen this state before, don't bother doing anything
			// again.
			$f = json_encode($q['data']);
			if (isset($visted[$f])) { continue; }
			$visted[$f] = true;

			foreach (getBuildOptions($bp, $materials, $robots) as $nextBuild) {
				$nextMaterials = $materials;
				$nextRobots = $robots;

				// Harvest
				foreach (array_keys($materials) as $type) {
					$nextMaterials[$type] += $nextRobots[$type];
				}

				if ($nextBuild != null) {
					// Remove used up materials
					foreach ($bp['costs'][$nextBuild] as $type => $cost) {
						$nextMaterials[$type] -= $cost;
					}

					// Build the Robot
					$nextRobots[$nextBuild]++;
				}

				// Enqueue the state.
				$queue->insert([$time + 1, $nextMaterials, $nextRobots], 0);
			}
		}

		return $geodeCount;
	}

	foreach ($blueprints as $bpid => $bp) {
		$blueprints[$bpid]['quality'] = buildBP($bp, 24) * $bp['num'];
	}

	$part1 = array_sum(array_map(fn($a) => $a['quality'], $blueprints));
	echo 'Part 1: ', $part1, "\n";


	foreach (array_filter($blueprints, fn($a) => $a['num'] <= 3) as $bpid => $bp) {
		$blueprints[$bpid]['geodeCount'] = buildBP($bp, 32);
	}
	$part2 = array_product(array_map(fn($a) => $a['geodeCount'] ?? 1, $blueprints));
	echo 'Part 2: ', $part2, "\n";
