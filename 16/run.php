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

    // Shortest distances between any two valves.
    // https://en.wikipedia.org/wiki/Floyd%E2%80%93Warshall_algorithm
	function getDistances($valves) {
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

		return $distances;
	}

	function getPressureRelease($valves, $distances) {

		$queue = new SPLQueue();
		$states = [];
		$visited = [];

		$queue->enqueue(['open' => [], 'pressure' => 0, 'minutes' => 0, 'loc' => 'AA', 'path' => ['AA']]);

		while (!$queue->isEmpty()) {
			$loc = $queue->dequeue();

			if ($loc['minutes'] == 30) {
				$states[] = $loc;
				continue;
			} else if ($loc['minutes'] > 30) {
				continue;
			}

			$currentPressure = 0;
			foreach (array_keys($loc['open']) as $open) { $currentPressure += $valves[$open]['rate']; }

			$options = [];

			// Wait here forever:
			if ($currentPressure > 0) {
				$newLoc = $loc;
				$diff = (30 - $newLoc['minutes']);
				$newLoc['pressure'] += $currentPressure * $diff;
				$newLoc['minutes'] = 30;
				$newLoc['path'][] = 'wait';
				// $newLoc['path'][] = '[T:' . $loc['minutes'] . ' WAIT A:' . $newLoc['minutes'] . ' P:' . $currentPressure . '*' . ($diff + 1) . ']';
				$options[] = $newLoc;
			}

			// Move somewhere we haven't opened yet that isn't broken.
			foreach (array_keys($valves) as $next) {
				if (!isset($loc['open'][$next]) && $valves[$next]['rate'] > 0) {
					$diff = $distances[$loc['loc']][$next];

					$newLoc = $loc;
					$newLoc['loc'] = $next;
					$newLoc['open'][$next] = true;
					$newLoc['minutes'] += $diff + 1;
					// $newLoc['path'][] = '[T:' . $loc['minutes'] . ' M:' . $next . ' A:' . $newLoc['minutes'] . ' P:' . $currentPressure . '*' . ($diff + 1) . ']';
					$newLoc['path'][] = $next;
					$newLoc['pressure'] += $currentPressure * ($diff + 1);
					$options[] = $newLoc;
				}
			}

			foreach ($options as $opt) {
				$s = implode(',', $opt['path']);
				// echo $s, "\n";
				if (!isset($visited[$s])) {
					$visited[$s] = true;
					$queue->enqueue($opt);
				}
			}
		}

		return $states;
	}

	$distances = getDistances($valves);
	$states = getPressureRelease($valves, $distances);
	usort($states, fn($a, $b) => $a['pressure'] <=> $b['pressure']);
	// foreach ($states as $state) { echo implode(',', $state['path']), ' => ', $state['pressure'], "\n"; }

	$part1 = $states[count($states) - 1]['pressure'];
	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
