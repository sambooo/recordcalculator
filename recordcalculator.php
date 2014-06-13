<?php
include("stats.php");


$serialized = trim(file_get_contents("foot"));
$updates = unserialize($serialized);

function time_pairs($gap) {
    global $updates;
    $pairs = array();
    $lower_bound = 0;

    for($i = 0; $i < count($updates); $i++) {
        $first_index = $i;
        $first = $updates[$first_index];
        $last_index  = $lower_bound;

        for($i = $lower_bound; $i < count($updates); $i++) {
            $u = $updates[$i];
            if ($u->time - $first->time > $gap)
                break;
            $last_index = $i;
            $lower_bound = $i;
        }

        $last = $updates[$last_index];
        $pairs[] = array($first, $last);
    }

    return $pairs;
}


function record($pairs, $skill) {
    $record_xp = 0;
    $record_pair = null;

    foreach($pairs as $pair) {
        $xp_difference = $pair[1]->xp[$skill] - $pair[0]->xp[$skill];
        if ($xp_difference > $record_xp) {
            $record_pair = $pair;
            $record_xp   = $xp_difference;
        }
    }

    $record = array("xp" => $record_xp, "time" => $record_pair[1]->time);
    return $record;
}

$times = array(1, 7, 31);

foreach($times as $time) {
    $pairs = time_pairs($time * 86400);

    for($i = 0; $i < $SKILL_COUNT; $i++) {
        $record = record($pairs, $i);
        $skill  = skill_name($i);
        echo $skill . " " . $time . " day record xp: " . $record["xp"] . "\n";
    }
}