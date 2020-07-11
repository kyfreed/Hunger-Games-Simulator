<?php

include_once 'utils.php';
include('Character.php');
$castSize = $_POST['castSize'];
$castObject = [];
for ($i = 0; $i < $castSize; $i++) {
    $r = strval(rand(0, 255));
    $g = strval(rand(0, 255));
    $b = strval(rand(0, 255));
    $character = new Character(htmlspecialchars($_POST["castName" . $i]), //Name
            ((array_key_exists("castNick" . $i, $_POST)) ? htmlspecialchars($_POST["castNick" . $i]) : $_POST["castName" . $i]), //Nickname
            $_POST["castGender" . $i], //Gender
            $_POST["castDisposition" . $i], //Disposition / Aggression
            ((array_key_exists("castStrength" . $i, $_POST)) ? (int) $_POST["castStrength" . $i] : 5), //Strength 
            ((array_key_exists("castHealth" . $i, $_POST)) ? (int) $_POST["castHealth" . $i] : 5), //Health
            ((array_key_exists("castDex" . $i, $_POST)) ? (int) $_POST["castDex" . $i] : 5), //Dexterity
            ((array_key_exists("castInt" . $i, $_POST)) ? (int) $_POST["castInt" . $i] : 5), //Intelligence
            ((array_key_exists("castCha" . $i, $_POST)) ? (int) $_POST["castCha" . $i] : 5), //Charisma
            ((array_key_exists("castImage" . $i, $_POST)) ? $_POST["castImage" . $i] : "generateImage.php?imageInitial=" . substr(htmlspecialchars($_POST["castName" . $i]), 0, 1) . "&r=" . $r . "&g=" . $g . "&b=" . $b), //Image
            $i); //Order Marker
    array_push($castObject, clone $character);
}
$_SESSION['castObject'] = $castObject;
$_SESSION['totalDead'] = [];
$_SESSION['counter'] = 1;
$_SESSION['place'] = count($castObject);
$_SESSION['castObjectToday'] = "";
$_SESSION['placeToday'] = "";
