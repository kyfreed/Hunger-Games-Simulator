<?php

include('Character.php');
include_once('utils.php');
$castJson = json_decode($_POST['castObject']);
$castObject = [];
foreach ($castJson as $character) {
    $charAttributes = [
        "name" => $character->name,
        "nick" => $character->nick,
        "gender" => $character->gender,
        "disposition" => $character->disposition,
        "strength" => $character->strength,
        "health" => $character->health,
        "dexterity" => $character->dexterity,
        "intelligence" => $character->intelligence,
        "charisma" => $character->charisma,
        "image" => $character->image,
        "orderMarker" => $character->orderMarker
    ];
    $char = new Character($charAttributes);
    array_push($castObject, clone $char);
}
$_SESSION['castObject'] = $castObject;
$_SESSION['totalDead'] = [];
$_SESSION['counter'] = 1;
$_SESSION['place'] = count($castObject);
$_SESSION['castObjectToday'] = "";
$_SESSION['placeToday'] = "";
