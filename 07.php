<?php

$input = file_get_contents(__DIR__ . '/07-input.txt');
$lines = explode("\n", trim($input));
$steps = [];

// Create list of steps from input
foreach($lines as $line) {
    $step1 = $line[5];
    if (!isset($steps[$step1]['step'])) {
        $steps[$step1]['step'] = $step1;
        $steps[$step1]['before'] = [];
        $steps[$step1]['depends'] = [];
    }

    $step2 = $line[36];
    if (!isset($steps[$step2]['step'])) {
        $steps[$step2]['step'] = $step2;
        $steps[$step2]['before'] = [];
        $steps[$step2]['depends'] = [];
    }

    $steps[$step1]['before'][] = $step2;
    $steps[$step2]['depends'][] = $step1;
}

$steps_left = $steps;
$solution = [];
while(count($steps_left)) {
    $valid_next_steps = [];

    foreach($steps_left as $step_left) {
        $ready = count(array_diff($step_left['depends'], $solution)) === 0;
        if ($ready) {
            $valid_next_steps[] = $step_left['step'];
        }
    }

    if (count($valid_next_steps) === 0 && count($steps_left)) {
        die('error' . __LINE__);
    }
    if (count($valid_next_steps) > 1) {
        sort($valid_next_steps);
    }

    $next_step = reset($valid_next_steps);
    unset($steps_left[$next_step]);
    $solution[] = $next_step;
}

echo 'Solution: ' . implode('', $solution) . PHP_EOL;
