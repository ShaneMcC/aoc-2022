#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	function fromSNAFU($in) {
		$sum = 0;
		$bit = 0;

		foreach (str_split(strrev($in)) as $c) {
			$pow = pow(5, $bit);

			if ($c == '=') {
				$sum += $pow * -2;
			} else if ($c == '-') {
				$sum += $pow * -1;
			} else {
				$sum += $pow * (int)$c;
			}

			$bit++;
		}

		return $sum;
	}


	function toSNAFU($in) {
		$snafu = "012=-";
    	if ($in >= 0 && $in < 5) {
        	return $snafu[(int)$in];
    	} else {
        	return toSNAFU(round($in / 5)) . $snafu[($in % 5)];
    	}
    }

	$sum = 0;
	foreach ($input as $line) {
		$sum += fromSNAFU($line);
	}

	$part1 = toSNAFU($sum);
	if (fromSNAFU($part1) != $sum) { echo '**NOT** '; }
	echo 'Part 1: ', $sum, ' => ', $part1, "\n";
