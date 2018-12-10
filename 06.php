<?php
ini_set('memory_limit', '512M');
$input = file_get_contents(__DIR__ . '/06-input.txt');
$input_lines = explode("\n", trim($input));
unset($input);

define('MAX_TOTAL_DISTANCE', 10000); // part 2

$coordinates = [];
$iterations = 0;

$limits = [
    'x_min' => PHP_INT_MAX,
    'x_max' => PHP_INT_MIN,
    'y_min' => PHP_INT_MAX,
    'y_max' => PHP_INT_MIN,
];

foreach($input_lines as $line) {
    [$x, $y] = explode(',', $line);
    $point = [
        'x' => (int)trim($x),
        'y' => (int)trim($y),
        'area' => 0,
        'infinite' => false,
    ];

    $coordinates[] = $point;

    if ($point['x'] < $limits['x_min']) {
        $limits['x_min'] = $point['x'];
    }
    if ($point['x'] > $limits['x_max']) {
        $limits['x_max'] = $point['x'];
    }
    if ($point['y'] < $limits['y_min']) {
        $limits['y_min'] = $point['y'];
    }
    if ($point['y'] > $limits['y_max']) {
        $limits['y_max'] = $point['y'];
    }
}

$locations = [];
$max_area_by_distance_sum = 0;
for($x = $limits['x_min']; $x <= $limits['x_max']; $x++) {
    for($y = $limits['y_min']; $y <= $limits['y_max']; $y++) {
        $iterations++;

        $is_edge_x = in_array($x, [$limits['x_min'], $limits['x_max']], true);
        $is_edge_y = in_array($y, [$limits['y_min'], $limits['y_max']], true);
        $is_edge = $is_edge_x || $is_edge_y;

        $current_location = [
            'x' => $x,
            'y' => $y,
            'min_distance' => PHP_INT_MAX,
            'coordinate_keys' => -1,
            'distance_sum' => 0,
        ];

        foreach($coordinates as $key => $coordinate) {
            $iterations++;

            $distance = manhattan_distance($current_location, $coordinate);

            $current_location['distance_sum'] += $distance;

            if ($distance <= $current_location['min_distance']) {
                if ($distance < $current_location['min_distance']) {
                    $current_location['coordinate_keys'] = [];
                }
                $current_location['min_distance'] = $distance;
                $current_location['coordinate_keys'][] = $key;
            }
        }


        if (count($current_location['coordinate_keys']) === 1) {
            $closest_coordinate_key = reset($current_location['coordinate_keys']);
            if ($is_edge) {
                // Flag coordinates which have the minimum distance on the edge because they have infinite area
                $coordinates[$closest_coordinate_key]['infinite'] = true;
            }
            else {
                // Increase area count for this coordinate if it's not on edge
                $coordinates[$closest_coordinate_key]['area']++;
            }
        }

        if ($current_location['distance_sum'] < MAX_TOTAL_DISTANCE) {
            $max_area_by_distance_sum++;
        }

        $locations[$x][$y] = $current_location;
    }
}

$max_area = PHP_INT_MIN;
foreach($coordinates as $coordinate) {
    if ($coordinate['area'] > $max_area && $coordinate['infinite'] === false) {
        $max_area = $coordinate['area'];
    }
}

echo 'Iterations: ' . number_format($iterations, 0, '.', "'") . PHP_EOL;
echo 'Max area: ' . $max_area . PHP_EOL;
echo 'Max area by distance sum: ' . $max_area_by_distance_sum . PHP_EOL;

// ---
function manhattan_distance(array $point1, array $point2): int
{
    return abs($point1['x'] - $point2['x']) + abs($point1['y'] - $point2['y']);
}
