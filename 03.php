<?php

$input = file_get_contents(__DIR__ . '/03-input.txt');
$claims = explode("\n", trim($input));
$claims_count = count($claims);

echo "Claims read: $claims_count\n";

$claimed_squares = [];
$claimed_mode_than_once = [];
$overlapping_claims = [];

foreach($claims as $claim) {
    $match = preg_match('/#(\d+) @ (\d+),(\d+): (\d+)x(\d+)/', $claim, $matches);
    list(, $claim_id, $left, $top, $width, $height) = array_map('intval', $matches);

    // Save claim for each square
    for($i = 0; $i < $width; $i++) {
        for($j = 0; $j < $height; $j++) {
            $l = $i + $left;
            $t = $j + $top;
            $coordinates = "{$l},{$t}";

            $claimed_squares[$coordinates][] = (int)$claim_id;

            if (count($claimed_squares[$coordinates]) > 1) {
                $claimed_mode_than_once[$coordinates] = true;
                foreach($claimed_squares[$coordinates] as $claim_id_for_square) {
                    $overlapping_claims[$claim_id_for_square] = true;
                }
            }
        }
    }

    if (!isset($overlapping_claims[$claim_id])) {
        $overlapping_claims[$claim_id] = false;
    }
}

echo 'Square inches requested more than once: ' . count($claimed_mode_than_once) . PHP_EOL;

echo "Claims that do not overlap: \n";
foreach($overlapping_claims as $claim_id => $overlapping) {
    if ($overlapping === false) {
        echo " - $claim_id\n";
    }
}


