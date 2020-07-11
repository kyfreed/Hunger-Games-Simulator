<?php

include 'Character.php';
include_once 'utils.php';
$newCharacters = $_POST['castIncrease'];
for ($i = 0; $i < $newCharacters; $i++) {
    $r = strval(rand(0, 255));
    $g = strval(rand(0, 255));
    $b = strval(rand(0, 255));
    $char = new Character("", "", "m", 3, 5, 5, 5, 5, 5, "", count($_SESSION['castObject']) + $i);
    array_push($_SESSION['castObject'], clone $char);
}


