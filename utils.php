<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

function nameList($array) {
    if (count($array) == 1) {
        return $array[0]->nick;
    } else if (count($array) == 2) {
        return $array[0]->nick . " and " . $array[1]->nick;
    } else {
        $listString = '';
        for ($i = 0; $i < count($array) - 1; $i++) {
            $listString .= $array[$i]->nick . ", ";
        }
        $listString .= "and " . end($array)->nick;
        return $listString;
    }
}

function series($array) {
    if (count($array) == 1) {
        return $array[0];
    } else if (count($array) == 2) {
        return $array[0] . " and " . $array[1];
    } else {
        $listString = '';
        for ($i = 0; $i < count($array) - 1; $i++) {
            $listString .= $array[$i] . ", ";
        }
        $listString .= "and " . end($array);
        return $listString;
    }
}

function f_rand($min = 0, $max = 1, $mul = 1000000) {
    if ($min > $max)
        return false;
    return mt_rand($min * $mul, $max * $mul) / $mul;
}

function startsWith($haystack, $needle) {
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle) {
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

function removeFromArray($value, $array) {
    if (!in_array($value, $array)) {
        return $array;
    }
    $newArray = $array;
    unset($newArray[array_search($value, $newArray)]);
    $newArray = array_values($newArray);
    return $newArray;
}

function print_r2($val) { //Prints an object to the page in a readable format.
    echo '<pre>';
    print_r($val);
    echo '</pre>';
}

function getCharacterByEvent($event) {
    $characterArray = [];
    foreach ($_SESSION['castObjectToday'] as $character) {
        if (strpos($event, $character->nick . " ") !== FALSE || strpos($event, $character->nick . ".") !== FALSE || strpos($event, $character->nick . ",") !== FALSE) {
            if (count($characterArray) == 0 || firstAfter($character->nick, $characterArray, $event) == -1) {
                array_push($characterArray, $character);
            } else {
                array_splice($characterArray, firstAfter($character->nick, $characterArray, $event), 0, array($character));
            }
        }
    }
    return $characterArray;
}

function firstAfter($sub, $array, $string) {
    $index = -1;
    $strPosAfter = strlen($string);
    for ($i = 0; $i < count($array); $i++) {
        if (strpos($string, $sub) < strpos($string, $array[$i]->nick) && strpos($string, $array[$i]->nick) < $strPosAfter) {
            $index = $i;
            $strPosAfter = strpos($string, $array[$i]->nick);
        }
    }
    return $index;
}

function showEvents($events) {
    echo "<div class='text-center'>";
    foreach ($events as $event) {
        foreach (getCharacterByEvent($event) as $character) {
            ?>
            <img src="<?= $character->image ?>" width="90" height="90"/>
            <?php
        }
        ?>
        <br>
        <?php
        echo $event;
    }
    echo "</div>";
}

function playersAlive() {
    $alive = 0;
    foreach ($_SESSION['castObjectToday'] as $character) {
        if ($character->status != "Dead") {
            $alive++;
        }
    }
    return $alive;
}

function array_copy($arr) {
    $newArray = array();
    foreach ($arr as $key => $value) {
        if (is_array($value))
            $newArray[$key] = array_copy($value);
        else if (is_object($value))
            $newArray[$key] = clone $value;
        else
            $newArray[$key] = $value;
    }
    return $newArray;
}
