<?php

$input = file_get_contents(__DIR__ . '/07-input.txt');
$lines = explode("\n", trim($input));
$steps = [];

// clearn debug file
file_put_contents(__DIR__ . '/07-debug.txt', '');

// Create list of steps from input
foreach($lines as $line) {
    $step1 = $line[5];
    if (!isset($steps[$step1]['step'])) {
        $steps[$step1]['step'] = $step1;
        $steps[$step1]['before'] = [];
    }

    $step2 = $line[36];
    if (!isset($steps[$step2]['step'])) {
        $steps[$step2]['step'] = $step2;
        $steps[$step2]['before'] = [];
    }

    $steps[$step1]['before'][] = $step2;
}

// Expand 'before' for each step
foreach(array_keys($steps) as $step) {
    $steps[$step]['original_before'] = $steps[$step]['before'];
    $list_to_expand = &$steps[$step]['before'];

    for($index = 0; isset($list_to_expand[$index]); $index++) {
        $step_to_append_from = $list_to_expand[$index];
        $list_to_append = $steps[$step_to_append_from]['before'];
        foreach($list_to_append as $step_to_append_to_list) {
            if (!in_array($step_to_append_to_list, $list_to_expand, true)) {
                $list_to_expand[] = $step_to_append_to_list;
            }
        }
    }
}

// Debug: print all steps and their 'before' list
ob_start();
uasort($steps, function($a, $b) { return count($b['before']) - count($a['before']); });
foreach($steps as $step) {
    sort($step['before']);
    sort($step['original_before']);
    echo $step['step'] . ' ' . implode('', $step['before']) . ' <= ' .implode('', $step['original_before']) . PHP_EOL;
}
$debug = ob_get_clean() . PHP_EOL;
file_put_contents(__DIR__ . '/07-debug.txt', $debug, FILE_APPEND);

//$bubble_sort = array_values($steps);
//$length = count($bubble_sort);
//for($i = 0; $i < $length -1; $i++) {
//    for($j = $i+1; $j < $length; $j++) {
//        echo "step[{$i}] vs step[{$j}]" . PHP_EOL;
//        $comparison = compare_steps($bubble_sort[$i], $bubble_sort[$j]);
//        if ($comparison > 0) {
//            $tmp = $bubble_sort[$i];
//            $bubble_sort[$i] = $bubble_sort[$j];
//            $bubble_sort[$j] = $tmp;
//            unset($tmp);
//        }
//        echo PHP_EOL;
//    }
//}
//print_steps($bubble_sort);

ob_start();
uasort($steps, 'compare_steps');
$debug = ob_get_clean();
echo "Sorted steps: " . print_steps($steps) . PHP_EOL;
file_put_contents(__DIR__ . '/07-debug.txt', $debug . PHP_EOL, FILE_APPEND);

// Validate against input
$solution = [];
foreach($steps as $step) {
    $solution[] = $step['step'];
}
$solution = array_flip($solution);
echo implode('', $solution) . PHP_EOL . implode('', array_keys($solution)) . PHP_EOL;
foreach($lines as $input_line) {
    $first = $input_line[5];
    $after = $input_line[36];
    if ($solution[$first] > $solution[$after]) {
        echo "$first before $after it's not respected" . PHP_EOL;
    }
}


/*
Wrong guesses:

BGCEKADFPRVWSYMZTIUXQJLHNO
GKRWBADEVMCFPSYZTIUXQJLHNO


*/






// ----

/**
 * @param array $steps
 * @return string
 */
function print_steps(array $steps): string
{
    $output = '';
    foreach ($steps as $step) {
        $output .= $step['step'];
    }
    return $output;
}

function compare_steps($a, $b): int
{
    echo "Comparison {$a['step']} (" . implode('', $a['before']) . ") vs {$b['step']} (" . implode('', $b['before']) . ') => ';

    if (in_array($b['step'], $a['before'], true)) {
        echo -1 . '*' . PHP_EOL;
        return -1;
    }
    if (in_array($a['step'], $b['before'], true)) {
        echo 1 . '*' . PHP_EOL;
        return 1;
    }
    return 0;

    echo strcmp($a['step'], $b['step']) . PHP_EOL;
    return strcmp($a['step'], $b['step']);
}
