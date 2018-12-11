<?php

$input_file_path = __DIR__ . '/08-input.txt';
$root_node = [];
$metadata_sum = 0;

$f = fopen($input_file_path, 'r');
read_node($f, $root_node, $metadata_sum);
fclose($f);

echo "Metadata sum: " . $metadata_sum . PHP_EOL;
echo "Root node value: " . $root_node['value'] . PHP_EOL;

function read_node($f, array &$node, int &$metadata_sum) {
    $no_of_children = read_number_from_file($f);
    $no_of_metadata = read_number_from_file($f);

    $node['children'] = array_fill(1, $no_of_children, []);
    $node['metadata'] = [];
    $node['value'] = 0;

    for($i = 1; $i <= $no_of_children; $i++) {
        read_node($f, $node['children'][$i], $metadata_sum);
    }

    for($i = 0; $i < $no_of_metadata; $i++) {
        $metadata_entry = read_number_from_file($f);

        $node['metadata'][] = $metadata_entry;
        $metadata_sum += $metadata_entry;

        if ($no_of_children) {
            if (isset($node['children'][$metadata_entry])) {
                $node['value'] += $node['children'][$metadata_entry]['value'];
            }
        }
        else {
            $node['value'] += $metadata_entry;
        }
    }
}

function read_number_from_file($f): int
{
    $buffer = '';
    $digits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    while (in_array(($c = fread($f, 1)), $digits, true)) {
        $buffer .= $c;
    }
    return (int)$buffer;
}

