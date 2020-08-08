<?php

include_once 'utils.php';
include('Character.php');
$castSize = $_POST['castSize'];
$castObject = [];
for ($i = 0; $i < $castSize; $i++) {
    $r = strval(rand(0, 255));
    $g = strval(rand(0, 255));
    $b = strval(rand(0, 255));
    $charAttributes = [
        "name" => htmlspecialchars($_POST["castName" . $i]),
        "nick" =>  ((array_key_exists("castNick" . $i, $_POST)) ? htmlspecialchars($_POST["castNick" . $i]) : $_POST["castName" . $i]),
        "gender" => $_POST["castGender" . $i],
        "disposition" => $_POST["castDisposition" . $i],
        "strength" => ((array_key_exists("castStrength" . $i, $_POST)) ? (int) $_POST["castStrength" . $i] : 5),
        "health" => ((array_key_exists("castHealth" . $i, $_POST)) ? (int) $_POST["castHealth" . $i] : 5),
        "dexterity" => ((array_key_exists("castDex" . $i, $_POST)) ? (int) $_POST["castDex" . $i] : 5),
        "intelligence" => ((array_key_exists("castInt" . $i, $_POST)) ? (int) $_POST["castInt" . $i] : 5),
        "charisma" => ((array_key_exists("castCha" . $i, $_POST)) ? (int) $_POST["castCha" . $i] : 5),
        "image" => ((array_key_exists("castImage" . $i, $_POST)) ? $_POST["castImage" . $i] : "generateImage.php?imageInitial=" . substr(htmlspecialchars($_POST["castName" . $i]), 0, 1) . "&r=" . $r . "&g=" . $g . "&b=" . $b),
        "orderMarker" => $i
    ];
    $character = new Character($charAttributes);
    array_push($castObject, clone $character);
}
$_SESSION['castObject'] = $castObject;
$_SESSION['totalDead'] = [];
$_SESSION['counter'] = 1;
$_SESSION['place'] = count($castObject);
$_SESSION['castObjectToday'] = "";
$_SESSION['placeToday'] = "";
