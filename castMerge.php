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
    $char = new Character($character->name, 
            $character->nick, 
            $character->gender, 
            $character->disposition, 
            $character->strength, 
            $character->health, 
            $character->dexterity, 
            $character->intelligence, 
            $character->charisma, 
            $character->image, 
            $character->orderMarker);
    array_push($charObjects, clone $char);
}
$_SESSION['castObject'] = $charObjects;
$_SESSION['place'] = count($charObjects);