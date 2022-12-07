#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	// Get commands and their output from the input
	$commands = [];
	$command = '';
	$commandOutput = [];
	foreach ($input as $line) {
		if (preg_match('#\$ (.*)#SADi', $line, $m)) {
			[, $cmd] = $m;
			if (!empty($command)) { $commands[] = ['cmd' => $command, 'output' => $commandOutput]; }
			$command = $cmd;
			$commandOutput = [];
		} else {
			$commandOutput[] = $line;
		}
	}
	if (!empty($command)) { $commands[] = ['cmd' => $command, 'output' => $commandOutput]; }

	// Parse the output of the commands into a tree.
	$tree = [];
	$pwd = '/';
	foreach ($commands as $c) {
		$cmd = $c['cmd'];
		$out = $c['output'];

		// Track current directory.
		if (preg_match('#cd (.*)#SADi', $cmd, $m)) {
			if ($m[1] == '..') {
				$pwd = (dirname($pwd) == '/') ? '/' : dirname($pwd) . '/';
			} else if ($m[1][0] == '/') {
				$pwd = $m[1];
			} else {
				$pwd .= $m[1] . '/';
			}
		// Track files in directory
		} else if ($cmd == 'ls') {
			foreach ($out as $lsout) {
				if (preg_match('#(\d+)\s+(.*)$#SADi', $lsout, $m)) {
					$file = $pwd . $m[2];
					$size = $m[1];

					while ($file != '/') {
						$file = (dirname($file) == '/') ? '/' : dirname($file) . '/';
						if (!isset($tree[$file])) { $tree[$file] = 0; }
						$tree[$file] += $size;
					}
				}
			}
		}
	}

	$part1 = array_sum(array_filter($tree, fn($d) => $d <= 100000));
	echo 'Part 1: ', $part1, "\n";

	$wantedSize = 30000000 - (70000000 - $tree['/']);
	$part2 = sorted('sort', array_filter($tree, fn($d) => $d > $wantedSize))[0] ?? 0;
	echo 'Part 2: ', $part2, "\n";
