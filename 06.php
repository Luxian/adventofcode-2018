<?php

$input = file_get_contents(__DIR__ . '/06-input.txt');
$input_lines = explode("\n", trim($input));
unset($input);

$coordinates = [];

$edges = [
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
    ];

    $coordinates[] = $point;

    if ($point['x'] < $edges['x_min']) {
        $edges['x_min'] = $point['x'];
    }
    if ($point['x'] > $edges['x_max']) {
        $edges['x_max'] = $point['x'];
    }
    if ($point['y'] < $edges['y_min']) {
        $edges['y_min'] = $point['x'];
    }
    if ($point['y'] > $edges['y_max']) {
        $edges['y_max'] = $point['y'];
    }
}
