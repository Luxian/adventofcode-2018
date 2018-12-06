<?php

$input = file_get_contents(__DIR__ . '/02-input.txt');
$codes = explode("\n", trim($input));
$codes_count = count($codes);

echo "Codes scaned: " . $codes_count . "\n";

$twos = $threes = 0;
foreach($codes as $code) {
	$letter_frequency = [];
	for($i = 0; $i<strlen($code); $i++) {
		$letter_frequency[$code[$i]] = isset($letter_frequency[$code[$i]]) ? $letter_frequency[$code[$i]] + 1 : 1;
	}
	
	$twos += array_search(2, $letter_frequency, true) !== false ? 1 : 0;
	$threes += array_search(3, $letter_frequency, true) !== false ? 1 : 0;
}
$checksum = $twos * $threes;

echo "Twos: $twos\nThrees: $threes\nChecksum: $checksum\n";


$pairs = [];
for($i = 0; $i < $codes_count - 1; $i++) {
	for($j = $i + 1; $j < $codes_count; $j++) {
		$code1 = $codes[$i];
		$code2 = $codes[$j];
		$different_char_count = 0;

		$letter = 0;
		while($different_char_count < 2 && $letter < strlen($code1)) {
			if ($code1[$letter] !== $code2[$letter]) {
				$different_char_count++;
			}
			$letter++;
		}
		
		if ($different_char_count === 1) {
			$pairs[$code1][] = $code2;
		}
	}
}

foreach($pairs as $first => $similar) {
	$code2 = reset($similar);
	$common_letters = '';

	for($i = 0; $i<strlen($first); $i++) {
		if ($first[$i] === $code2[$i]) {
			$common_letters .= $first[$i];
		}
	}
	
	echo implode(', ', array_merge([$first], $similar)) . ' have "' . $common_letters . '" in common' . PHP_EOL;
}

print_r($pairs);


