<?php

$input = file_get_contents(__DIR__ . '/07-input.txt');
$lines = explode("\n", trim($input));

$steps = [];
foreach($lines as $line) {
    $step1 = $line[5];
    $step2 = $line[36];
    if (!isset($steps[$step1]['step'])) {
        $steps[$step1]['step'] = $step1;
        $steps[$step1]['before'] = [];
    }
    if (!isset($steps[$step2]['step'])) {
        $steps[$step2]['step'] = $step2;
        $steps[$step2]['before'] = [];
    }
    $steps[$step1]['before'][] = $step2;
}

foreach(array_keys($steps) as $step) {
    $index = 0;
    $expanded_before = &$steps[$step]['before'];
    while($index < count($expanded_before)) {
        $current_before_element = $expanded_before[$index];
        if (isset($steps[$current_before_element]['before'])) {
            $next_level_before = $steps[$current_before_element]['before'];
            foreach($next_level_before as $substep) {
                if (!in_array($substep, $expanded_before, true)) {
                    $expanded_before[] = $substep;
                }
            }
        }
        $index++;
    }
}

//file_put_contents(__DIR__ . '/07-debug.txt', print_r($steps, true));


//print_steps($steps);

$bubble_sort = array_values($steps);
$length = count($bubble_sort);
for($i = 0; $i < $length -1; $i++) {
    for($j = $i+1; $j < $length; $j++) {
        $comparison = compare_steps($bubble_sort[$i], $bubble_sort[$j]);
        if ($comparison > 0) {
            $tmp = $bubble_sort[$i];
            $bubble_sort[$i] = $bubble_sort[$j];
            $bubble_sort[$j] = $tmp;
            unset($tmp);
        }
        print_steps($steps);
    }
}

uasort($steps, 'compare_steps');


//print_steps($steps);
print_steps($bubble_sort);


// ----

/**
 * @param array $steps
 */
function print_steps(array $steps): void
{
    echo "Steps: ";
    foreach ($steps as $step) {
        echo $step['step'];
    }
    echo PHP_EOL;
}

/**
 * @return Closure
 */
function compare_steps($a, $b): int
{
    echo "{$a['step']} vs {$b['step']} => ";

    if (in_array($b['step'], $a['before'], true)) {
        echo -1 . '*' . PHP_EOL;
        return -1;
    }
    if (in_array($a['step'], $b['before'], true)) {
        echo 1 . '*' . PHP_EOL;
        return 1;
    }

    echo strcmp($a['step'], $b['step']) . PHP_EOL;
    return strcmp($a['step'], $b['step']);
}