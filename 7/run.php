#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$tree = [];
	$pwd = '/';
	foreach ($input as $line) {
		if (preg_match('#\$ cd (.*)#SADi', $line, $m)) {
			if ($m[1] == '..') {
				$pwd = (dirname($pwd) == '/') ? '/' : dirname($pwd) . '/';
			} else if ($m[1][0] == '/') {
				$pwd = $m[1];
			} else {
				$pwd .= $m[1] . '/';
			}
		} else if (preg_match('#(\d+)\s+(.*)$#SADi', $line, $m)) {
			$file = $pwd . $m[2];
			while ($file != '/') {
				$file = (dirname($file) == '/') ? '/' : dirname($file) . '/';
				if (!isset($tree[$file])) { $tree[$file] = 0; }
				$tree[$file] += $m[1];
			}
		}
	}

	$part1 = array_sum(array_filter($tree, fn($d) => $d <= 100000));
	echo 'Part 1: ', $part1, "\n";

	$wantedSize = 30000000 - (70000000 - $tree['/']);
	$part2 = sorted('sort', array_filter($tree, fn($d) => $d > $wantedSize))[0] ?? 0;
	echo 'Part 2: ', $part2, "\n";
