#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$hasLS = [];
	$ignore = false;
	$tree = [];
	$pwd = '/';
	foreach ($input as $line) {
		$bits = explode(' ', $line);
		if ($bits[0] == '$' && $bits[1] == 'cd') {
			$ignore = false;
			if ($bits[2] == '..') {
				$pwd = (dirname($pwd) == '/') ? '/' : dirname($pwd) . '/';
			} else if ($bits[2][0] == '/') {
				$pwd = $bits[2];
			} else {
				$pwd .= $bits[2] . '/';
			}
		} else if ($bits[0] == '$' && $bits[1] == 'ls') {
			if (isset($hasLS[$pwd])) {
				$ignore = true;
			} else {
				$hasLS[$pwd] = true;
			}
		} else if (!$ignore && is_numeric($bits[0])) {
			$file = $pwd . $bits[1];
			while ($file != '/') {
				$file = (dirname($file) == '/') ? '/' : dirname($file) . '/';
				if (!isset($tree[$file])) { $tree[$file] = 0; }
				$tree[$file] += $bits[0];
			}
		}
	}

	$part1 = array_sum(array_filter($tree, fn($d) => $d <= 100000));
	echo 'Part 1: ', $part1, "\n";

	$wantedSize = 30000000 - (70000000 - $tree['/']);
	$part2 = sorted('sort', array_filter($tree, fn($d) => $d > $wantedSize))[0] ?? 0;
	echo 'Part 2: ', $part2, "\n";
