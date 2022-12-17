#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$valves = [];
	foreach ($input as $line) {
		preg_match('#Valve (.*) has flow rate=(.*); tunnels? leads? to valves? (.*)#SADi', $line, $m);
		[, $valve, $rate, $next] = $m;
		$valves[$valve] = ['valve' => $valve, 'rate' => $rate, 'next' => explode(', ', $next)];
	}

	$distances = [];
	foreach ($valves as $valve) {
		$distances[$valve['valve']] = [$valve['valve'] => 0];

		foreach ($valve['next'] as $next) {
			$distances[$valve['valve']][$next] = 1;
		}
	}

	foreach (array_keys($valves) as $k) {
		foreach (array_keys($valves) as $i) {
			foreach (array_keys($valves) as $j) {
				if (isset($distances[$i][$k]) && isset($distances[$k][$j])) {
					if (!isset($distances[$i][$j]) || ($distances[$i][$j] > $distances[$i][$k] + $distances[$k][$j])) {
						$distances[$i][$j] = $distances[$i][$k] + $distances[$k][$j];
					}
				}
			}
		}
	}

	$valves = array_filter($valves, fn($a) => $a['rate'] > 0);

	function getPressureRelease($valves, $distances, $maxTime = 30) {
		$queue = new SPLQueue();
		$states = [];
		$visited = [];

		$queue->enqueue(['open' => [], 'flow' => 0, 'pressure' => 0, 'minutes' => 0, 'loc' => 'AA', 'path' => []]);

		while (!$queue->isEmpty()) {
			$loc = $queue->dequeue();
			$options = [];

			// Wait here forever:
			if ($loc['flow'] > 0) {
				$newLoc = $loc;
				$diff = ($maxTime - $newLoc['minutes']);
				$newLoc['pressure'] += $loc['flow'] * $diff;
				$newLoc['minutes'] = $maxTime;
				$states[] = $newLoc;
			}

			// Move somewhere we haven't opened yet that isn't broken.
			foreach (array_keys($valves) as $next) {
				if (!isset($loc['open'][$next])) {
					// Time taken to move there, +1 to open it.
					$timeTaken = $distances[$loc['loc']][$next] + 1;

					$newLoc = $loc;
					$newLoc['loc'] = $next;
					$newLoc['open'][$next] = true;
					$newLoc['minutes'] += $timeTaken;
					$newLoc['path'][] = $next;
					$newLoc['pressure'] += $loc['flow'] * ($timeTaken);
					$newLoc['flow'] += $valves[$next]['rate'];
					if ($newLoc['minutes'] < $maxTime) {
						$options[] = $newLoc;
					}
				}
			}

			foreach ($options as $opt) {
				$s = implode(',', $opt['path']);
				if (!isset($visited[$s])) {
					$visited[$s] = true;
					$queue->enqueue($opt);
				}
			}
		}

		return $states;
	}

	// Get all possible states for 30 minutes, return the best.

	$states30 = getPressureRelease($valves, $distances, 30);
	usort($states30, fn($a, $b) => $a['pressure'] <=> $b['pressure']);
	$part1 = $states30[count($states30) - 1]['pressure'];
	echo 'Part 1: ', $part1, "\n";

	// Get all possible states for 26 minutes.
	$states26 = getPressureRelease($valves, $distances, 26);
	$part2 = 0;

	// Find the best costs for each combination.
	$bestPaths = [];
	foreach ($states26 as $s) {
		$nodes = implode(',', sorted('sort', $s['path']));
		$bestPaths[$nodes] = max($bestPaths[$nodes] ?? PHP_INT_MIN, $s['pressure']);
	}

	// Now find the best pair.
	foreach ($bestPaths as $s1 => $p1) {
		foreach ($bestPaths as $s2 => $p2) {
			$d = array_intersect(explode(',', $s1), explode(',', $s2));
			if (empty($d)) {
				$part2 = max($part2, $p1 + $p2);
			}
		}
	}
	echo 'Part 2: ', $part2, "\n";
