#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	class ElfItem {
		public $value = null;
		public $nextMixedItem = null;

		public $next = null;
		public $prev = null;

		public function __construct($i) { $this->value = $i; }
	}

	function buildItems($input, $key = 1) {
		// Build the list.
		$zero = null;
		$first = null;
		$prev = null;
		$count = 0;
		foreach ($input as $i) {
			$item = new ElfItem($i * $key);

			if ($i == 0) { $zero = $item; }

			if ($first == null) { $first = $item; }

			if ($prev != null) {
				$prev->nextMixedItem = $item;
				$prev->next = $item;
				$item->prev = $prev;
			}
			$prev = $item;

			$count++;
		}
		// Make the list circular
		$prev->next = $first;
		$first->prev = $prev;

		return [$first, $zero, $count];
	}

	function displayItems($first) {
		$item = $first;
		do {
			echo '"', $item->value, '"', "\n";

			$item = $item->next;
		} while ($item != $first);
	}

	function mixItems($first, $count) {
		$item = $first;
		do {

			if ($item->value != 0) {
				// Mix the value.
				$oldPrev = $item->prev;
				$oldNext = $item->next;

				// remove item from the list
				$oldPrev->next = $oldNext;
				$oldNext->prev = $oldPrev;

				$check = $item;

				for ($i = 0; $i < (abs($item->value) % ($count - 1)); $i++) {
					$check = $item->value < 0 ? $check->prev : $check->next;
				}

				// Move back one further for correct re-attachment point.
				if ($item->value < 0) { $check = $check->prev; }

				// Reattach the item.
				$checkNext = $check->next;
				$check->next = $item;
				$item->prev = $check;
				$item->next = $checkNext;
				$checkNext->prev = $item;
			}

			$item = $item->nextMixedItem;
		} while ($item != null);
	}

	function getBits($zero) {
		$bits = [];
		$item = $zero;
		for ($i = 1; $i <= 3000; $i++) {
			$item = $item->next;

			if ($i % 1000 == 0) { $bits[] = $item->value; }
		}

		return $bits;
	}

	[$first, $zero, $count] = buildItems($input);
	mixItems($first, $count);
	$part1 = array_sum(getBits($zero));
	echo 'Part 1: ', $part1, "\n";

	[$first, $zero, $count] = buildItems($input, 811589153);
	for ($i = 1; $i <= 10; $i++) {
		mixItems($first, $count);
	}
	$part2 = array_sum(getBits($zero));
	echo 'Part 2: ', $part2, "\n";
