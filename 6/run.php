#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	function getStartMarker($input, $length) {
		for ($i = $length - 1; $i < strlen($input); $i++) {
			$last = str_split(substr($input, $i - $length - 1, $length));
			if (count(array_unique($last)) == $length) {
				return $i + 1;
			}
		}
	}

	echo 'Part 1: ', getStartMarker($input, 4), "\n";
	echo 'Part 2: ', getStartMarker($input, 14), "\n";
