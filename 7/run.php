#!/usr/bin/php
<?php
    $__CLI['long'] = ['files', 'full'];
    $__CLI['extrahelp'] = [];
    $__CLI['extrahelp'][] = '      --files              Include files in debug output.';
    $__CLI['extrahelp'][] = '      --full               Show full paths in debug output.';


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
			$ignore = $hasLS[$pwd] ?? false;
			$hasLS[$pwd] = true;
		} else if (!$ignore && is_numeric($bits[0])) {
			$file = $pwd . $bits[1];

			if (isDebug() && isset($__CLIOPTS['files'])) { $tree[$file] = $bits[0]; }

			while ($file != '/') {
				$file = (dirname($file) == '/') ? '/' : dirname($file) . '/';
				if (!isset($tree[$file])) { $tree[$file] = 0; }
				$tree[$file] += $bits[0];
			}
		}
	}

	if (isDebug()) {
		ksort($tree);
		foreach ($tree as $file => $size) {
			$depth = substr_count($file, '/');
			$type = $file[strlen($file) - 1] == '/' ? 'dir' : 'file';
			if ($type == 'dir') { $depth--; }
			$displayName = (isset($__CLIOPTS['full']) ? $file : (basename($file) == '' ? '/' : basename($file)));
			echo str_repeat("  ", $depth), '- ', $displayName, ' (', $type, ', size=', $size, ')', "\n";
		}
		// Remove non-directories
		foreach (array_filter(array_keys($tree), fn($f) => $f[strlen($f) - 1] != '/') as $file) {
			unset($tree[$file]);
		}
	}

	$part1 = array_sum(array_filter($tree, fn($d) => $d <= 100000));
	echo 'Part 1: ', $part1, "\n";

	$wantedSize = 30000000 - (70000000 - $tree['/']);
	$part2 = sorted('sort', array_filter($tree, fn($d) => $d > $wantedSize))[0] ?? 0;
	echo 'Part 2: ', $part2, "\n";
