<?php
include 'Character.php';
include_once 'utils.php';
$charObjects = [];
$castObject = json_decode($_POST['cast0']);
for($i = 1; $i < count($_POST); $i++){
    $castObject = array_merge($castObject, json_decode($_POST['cast' . $i]));
}
for($i = 0; $i < count($castObject); $i++){
    $castObject[$i]->orderMarker = $i;
}
foreach($castObject as $character){
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
    array_push($charObjects, clone $char);
}
$_SESSION['castObject'] = $charObjects;
$_SESSION['place'] = count($charObjects);