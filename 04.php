<?php

$input = file_get_contents(__DIR__ . '/04-input.txt');
$lines = explode("\n", trim($input));
sort($lines);
$entries = count($lines);

$guardians = [];


$current_line = 0;
$guardian = -1;
$started_asleep = -1;
while ($current_line < $entries)
{
    $line = $lines[$current_line];
    $year = (int)substr($line, 1, 4);
    $month = (int)substr($line, 6, 2);
    $day = (int)substr($line, 9, 2);
    $hour = (int)substr($line, 12, 2);
    $minute = (int)substr($line, 15, 2);
    $text = substr($line, 19);

    if (strpos($text, 'begins shift') !== false) {
        if ($started_asleep !== -1) {
            echo "Expecting guarding #{$guardian} to wake up before a new guarding starts shift on line {$line}. Found '{$line}'\n";
            exit(1);
        }

        $guardian = (int)substr($text, 7, strpos($line, ' ', 17));
    }
    elseif ($guardian !== -1) {
        if ($started_asleep === -1) {
            if ($text !== 'falls asleep') {
                echo "Expecting guarding #{$guardian} to fall asleep on line {$current_line}. Found '{$text}''\n";
                exit(1);
            }
            $started_asleep = $minute;
        }
        elseif ($started_asleep !== -1 && $text !== 'wakes up') {
            echo "Expecting guarding #{$guardian} to wake up on line {$current_line}. Found '{$line}'\n'";
            exit(1);
        }
        else {
            if (!isset($guardians[$guardian]['sleep_total'])) {
                $guardians[$guardian]['sleep_total'] = 0;
            }

            $sleep_duration = $minute - $started_asleep;
            $guardians[$guardian]['sleep_total'] += $sleep_duration;
            $guardians[$guardian]['sleeps'][] = [
                'start' => $started_asleep,
                'end' => $minute,
                'total' => $sleep_duration
            ];

            $started_asleep = -1;
        }
    }
    else {
        echo "Expecting a guardian starting shift at line {$current_line}. Found: {$line}\n";
        exit(1);
    }

    $current_line++;
}

foreach($guardians as $guardian => $info) {

    $minutes_asleep_frequency = [];
    foreach($info['sleeps'] as $nap) {
        for($minute = $nap['start']; $minute < $nap['end']; $minute++) {
            if (!isset($minutes_asleep_frequency[$minute])) {
                $minutes_asleep_frequency[$minute] = 0;
            }
            $minutes_asleep_frequency[$minute]++;
        }
    }

    $minutes_asleep_frequency_sorted = $minutes_asleep_frequency;
    asort($minutes_asleep_frequency_sorted);
    $minutes_asleep_frequency_sorted = array_reverse($minutes_asleep_frequency_sorted, true);

    $guardians[$guardian]['id'] = $guardian;
    $guardians[$guardian]['minutes_asleep_frequency'] = $minutes_asleep_frequency;
    $guardians[$guardian]['minutes_asleep_frequency_sorted'] = $minutes_asleep_frequency_sorted;
    $guardians[$guardian]['most_often_slept_minute_naps'] = reset($minutes_asleep_frequency_sorted);
    $guardians[$guardian]['most_often_slept_minute'] = key($minutes_asleep_frequency_sorted);
}

uasort($guardians, function($a, $b) {
    return $b['sleep_total'] - $a['sleep_total'];
});
$guardian_1 = reset($guardians);
echo "Guardian #{$guardian_1['id']} slept the most ({$guardian_1['sleep_total']} minutes), and it's most often asleep at 00:{$guardian_1['most_often_slept_minute']}.\n";


uasort($guardians, function($a, $b) {
   return $b['most_often_slept_minute_naps'] - $a['most_often_slept_minute_naps'];
});
$guardian_2 = reset($guardians);
echo "Guardian #{$guardian_2['id']} keeps sleeping on the same minute (00:{$guardian_2['most_often_slept_minute']}) more often than any other ({$guardian_2['most_often_slept_minute_naps']}).\n";
