<?php

$input = file_get_contents(__DIR__ . '/07-input.txt');
$lines = explode("\n", trim($input));
$steps = [];
$number_of_workers = 5;
$solution = [];
$seconds = 0;

// Create list of steps from input
foreach($lines as $line) {
    $step1 = $line[5];
    if (!isset($steps[$step1]['step'])) {
        $steps[$step1] = get_initial_step_structure($step1);
    }

    $step2 = $line[36];
    if (!isset($steps[$step2]['step'])) {
        $steps[$step2] = get_initial_step_structure($step2);
    }

    $steps[$step1]['before'][] = $step2;
    $steps[$step2]['depends'][] = $step1;
}

while(count($steps)) {
    $steps_that_can_be_worked_on = [];

    // Find steps that can be worked on
    foreach($steps as $step) {
        $ready = count(array_diff($step['depends'], $solution)) === 0;
        if ($ready) {
            $steps_that_can_be_worked_on[] = $step['step'];
        }
    }
    unset($step);

    if (count($steps_that_can_be_worked_on) === 0 && count($steps)) {
        die('Error: could not find solution');
    }

    // Decide which steps can be worked on this second
    $steps_worked_this_second = 0;
    uasort(
        $steps_that_can_be_worked_on,
        function (string $step1, string $step2) use ($steps) {
            // steps completed are not not being worked on
            if ($steps[$step1]['cost'] === 0){
              return $steps[$step2]['cost'] === 0 ? 0 : 1;
            }
            if ($steps[$step2]['cost'] === 0) {
                return -1;
            }
            // if one is already started work on that first
            if ($steps[$step1]['started'] !== $steps[$step2]['started']) {
                return $steps[$step1]['started'] ? -1 : 1;
            }

            return strcmp($steps[$step1]['step'], $steps[$step2]['step']);
        }
    );
    foreach($steps_that_can_be_worked_on as $step) {
        if ($steps[$step]['cost']) {
            $steps[$step]['cost']--;
            $steps[$step]['started'] = true;
            $steps_worked_this_second++;

            if ($steps_worked_this_second === $number_of_workers) {
                break;
            }
        }
    }
    unset($step);
    $seconds++;

    $steps_that_are_completed = array_filter(
        $steps_that_can_be_worked_on,
        function(string $step) use ($steps) {
            return $steps[$step]['cost'] === 0;
        }
    );
    if (count($steps_that_are_completed) > 1) {
        sort($steps_that_are_completed);
    }

    $next_step = reset($steps_that_are_completed);
    if ($steps[$next_step]['cost'] === 0) {
        unset($steps[$next_step]);
        $solution[] = $next_step;
    }
}

echo 'Solution: ' . implode('', $solution) . PHP_EOL;
echo 'Duration: ' . $seconds;

// -----
function get_initial_step_structure(string $step): array
{
    $ascii_to_cost_offset = ord('A') - 1;
    return [
        'step' => $step,
        'depends' => [],
        'cost' => 60 + (ord($step) - $ascii_to_cost_offset),
        'started' => false,
    ];
}
