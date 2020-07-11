<?php
include_once 'utils.php';
$castObject = [];
for($i = 0; $i < count($_POST); $i++){
    array_push($castObject, json_decode($_POST['char' . $i]));
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
