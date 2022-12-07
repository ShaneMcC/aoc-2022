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

		if (preg_match('#cd (.*)#SADi', $cmd, $m)) {
			if ($m[1] == '..') {
				$pwd = (dirname($pwd) == '/') ? '/' : dirname($pwd) . '/';
			} else if ($m[1][0] == '/') {
				$pwd = $m[1];
			} else {
				$pwd .= $m[1] . '/';
			}

			if (!isset($tree[$pwd])) {
				$tree[$pwd] = ['size' => FALSE, 'contents' => []];
			}
		} else if ($cmd == 'ls') {
			foreach ($out as $lsout) {
				if (preg_match('#(\d+)\s+(.*)$#SADi', $lsout, $m)) {
					$tree[$pwd]['contents'][$m[2]] = ['type' => 'file', 'size' => $m[1]];
				} else if (preg_match('#dir\s+(.*)$#SADi', $lsout, $m)) {
					$tree[$pwd]['contents'][$m[1]] = ['type' => 'dir'];
				}
			}
		}
	}

	// Recursively correct the directory sizes in a given tree.
	function updateDirectorySizes(&$tree, $directory = '/') {
		foreach ($tree[$directory]['contents'] as $f => $c) {
			if ($c['type'] == 'file') {
				$tree[$directory]['size'] += $c['size'];
			}

			if ($c['type'] == 'dir') {
				$dirName = $directory . $f . '/';

				if ($tree[$dirName]['size'] == FALSE) {
					updateDirectorySizes($tree, $dirName);
				}
				$tree[$directory]['size'] += $tree[$dirName]['size'];
			}
		}
	}
	updateDirectorySizes($tree);

	// echo json_encode($tree, JSON_PRETTY_PRINT), "\n";

	$part1 = array_sum(array_map(fn($d) => $d['size'], array_filter($tree, fn($d) => $d['size'] <= 100000)));
	echo 'Part 1: ', $part1, "\n";


	$wantedSize = 30000000 - (70000000 - $tree['/']['size']);
	$possible = array_map(fn($d) => $d['size'], array_filter($tree, fn($d) => $d['size'] > $wantedSize));
	sort($possible);
	$part2 = $possible[0];
	echo 'Part 2: ', $part2, "\n";
