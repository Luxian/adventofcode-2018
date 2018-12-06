<?php

$f = fopen(__DIR__ . '/05-input.txt', 'r');

$ignoredUnits = ['_'];
$currentlyIgnoreUnitIndex = 0;
$reactedPolymerForEachIgnoredUnit = [];
$minLength = PHP_INT_MAX;

while(isset($ignoredUnits[$currentlyIgnoreUnitIndex])) {
    $currentlyIgnoredUnit = $ignoredUnits[$currentlyIgnoreUnitIndex];

    $reactedPolymer = '';
    rewind($f);
    while (!feof($f) && ($nextUnit = fread($f, 1)) !== false) {
        $nextUnitLowerCase = strtolower($nextUnit);
        $lastUnit = substr($reactedPolymer, -1);
        if ($lastUnit !== $nextUnit && strtolower($lastUnit) === $nextUnitLowerCase) {
            $reactedPolymer = substr($reactedPolymer, 0, -1);
        }
        elseif ($nextUnitLowerCase !== $currentlyIgnoredUnit) {
            $reactedPolymer .= $nextUnit;
        }
        if (!in_array($nextUnitLowerCase, $ignoredUnits, true)) {
            $ignoredUnits[] = $nextUnitLowerCase;
        }
    }
    $reactedPolymerForEachIgnoredUnit[$currentlyIgnoredUnit] = $reactedPolymer;

    if (strlen($reactedPolymer) < $minLength) {
        $minLength = strlen($reactedPolymer);
    }

    // Repeat with the next unit on the list of ignore
    $currentlyIgnoreUnitIndex++;
}


fclose($f);

echo 'Reacted polymer length is ' . strlen($reactedPolymerForEachIgnoredUnit['_']) . PHP_EOL;
echo 'Smallest polymer that can be obtained by ignoring one unit type is ' . $minLength . PHP_EOL;

file_put_contents(__DIR__ . '/05-input-reacted.txt', $reactedPolymerForEachIgnoredUnit['_']);
