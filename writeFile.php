<?php
include('Character.php');
include_once('utils.php');
session_start();
$castJson = json_decode($_POST['castObject']);
$castObject = [];
foreach($castJson as $character){
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
    array_push($castObject, clone $char);
}
$_SESSION['castObject'] = $castObject;
$_SESSION['totalDead'] = [];
$_SESSION['counter'] = 1;
$_SESSION['place'] = count($castObject);
$_SESSION['castObjectToday'] = "";
$_SESSION['placeToday'] = "";