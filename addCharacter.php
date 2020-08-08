<?php

include 'Character.php';
include_once 'utils.php';
$newCharacters = $_POST['castIncrease'];
for ($i = 0; $i < $newCharacters; $i++) {
    $charAttributes = [
        "name" => "",
        "nick" => "",
        "gender" => "m",
        "disposition" => 3,
        "strength" => 5,
        "health" => 5,
        "dexterity" => 5,
        "intelligence" => 5,
        "charisma" => 5,
        "image" => "",
        "orderMarker" => count($_SESSION['castObject']) + $i
    ];
    $char = new Character($charAttributes);
    array_push($_SESSION['castObject'], clone $char);
}


