<?php

$input = file_get_contents(__DIR__ . '/01-input.txt');
$calibrations = explode("\n", trim($input));
$calibrations_count = count($calibrations);

$sum = 0;
$frequncy_history = [0 => 1];
$first_repeated_frequency = null;

foreach($calibrations as $number) {
	$sum += (int)$number;
	$frequency_appeared_before = isset($frequncy_history[$sum]);
	$frequncy_history[$sum] = $frequency_appeared_before ? $frequncy_history[$sum] + 1 : 1;


	if ($first_repeated_frequency === null && $frequncy_history[$sum] === 2) {
		$first_repeated_frequency = $sum;
	}
}
echo "After " . $calibrations_count . " calibration changes the frequency is " . $sum . "\n";

$index = 0;
while($first_repeated_frequency === null) {
	$sum = $sum + $calibrations[$index++ % $calibrations_count];
	$frequency_appeared_before = isset($frequncy_history[$sum]);
	$frequncy_history[$sum] = $frequency_appeared_before ? $frequncy_history[$sum] + 1 : 1;
	if ($first_repeated_frequency === null && $frequncy_history[$sum] === 2) {
		$first_repeated_frequency = $sum;
	}
}


echo "The first repeating frequency was: " . $first_repeated_frequency . "\n";
