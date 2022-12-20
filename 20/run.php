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

	function buildItems($input) {
		// Build the list.
		$zero = null;
		$first = null;
		$prev = null;
		foreach ($input as $i) {
			$item = new ElfItem($i);

			if ($i == 0) { $zero = $item; }

			if ($first == null) { $first = $item; }

			if ($prev != null) {
				$prev->nextMixedItem = $item;
				$prev->next = $item;
				$item->prev = $prev;
			}
			$prev = $item;
		}
		// Make the list circular
		$item->next = $first;
		$first->prev = $item;

		return [$first, $zero];
	}

	function displayItems($first) {
		$item = $first;
		do {
			echo '"', $item->value, '"', "\n";

			$item = $item->next;
		} while ($item != $first);
	}

	function mixItems($first) {
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
				for ($i = 0; $i < abs($item->value); $i++) {
					$check = $item->value < 0 ? $check->prev : $check->next;
				}

				// Move back one further for correct re-attachment.
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

	[$first, $zero] = buildItems($input);
	mixItems($first);
	displayItems($first);
	$part1 = array_sum(getBits($zero));
	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
