<?php
session_start();
$castSize = $_POST['castSize'];
$castObject = [];
//print_r2($_POST);
for($i = 0; $i < $castSize; $i++){
    $r = strval(rand(0, 255));
    $g = strval(rand(0, 255));
    $b = strval(rand(0, 255));
    $tempObject->name = htmlspecialchars($_POST["castName" . $i]); //Name used on start and stats screens.
    $tempObject->nick = ((array_key_exists("castNick" . $i, $_POST)) ? htmlspecialchars($_POST["castNick" . $i]) : $tempObject->name); //Name used in the main game.
    $tempObject->gender = $_POST["castGender" . $i];
    $tempObject->disposition = $_POST["castDisposition" . $i]; //Basically aggression. I would have changed the name but I didn't want to search for all its uses.
    $tempObject->strength = ((array_key_exists("castStrength" . $i, $_POST)) ? (int) $_POST["castStrength" . $i] : 5);
    $tempObject->health = ((array_key_exists("castHealth" . $i, $_POST)) ? (int) $_POST["castHealth" . $i] : 5);
    $tempObject->maxStrength = $tempObject->strength;
    $tempObject->maxHealth = $tempObject->health;
    $tempObject->modifiedStrength = $tempObject->strength / 5;
    $tempObject->dexterity = ((array_key_exists("castDex" . $i, $_POST)) ? (int) $_POST["castDex" . $i] : 5); //Dexterity determines probability of successful attacks and dodges.
    $tempObject->intelligence = ((array_key_exists("castInt" . $i, $_POST)) ? (int) $_POST["castInt" . $i] : 5); //Intelligence determines probability of finding food or water, avoiding bear traps, and detecting poison.
    $tempObject->charisma = ((array_key_exists("castCha" . $i, $_POST)) ? (int) $_POST["castCha" . $i] : 5); //Charisma determines probability of being sponsored and characters allying with them.
    $tempObject->defense = 0; //Exists in case defensive items are added.
    $tempObject->image = ((array_key_exists("castImage" . $i, $_POST)) ? $_POST["castImage" . $i] : "generateImage.php?imageInitial=" . substr(htmlspecialchars($_POST["castName" . $i]),0,1) . "&r=" . $r . "&g=" . $g . "&b=" . $b);
    $tempObject->status = "Alive";
    $tempObject->actionTaken = "false";
    $tempObject->daysOfFood = 1; //daysofFood and daysofWater are set to 1 so that characters don't immediately begin starving after the Bloodbath.
    $tempObject->daysWithoutFood = 0;
    $tempObject->daysOfWater = 1;
    $tempObject->desiredItems = [];
    $tempObject->inventory = [];
    $tempObject->arrows = 0;
    $tempObject->explosivesPlanted = 0;
    $tempObject->memberOfAlliance = -1;
    $tempObject->equippedItem = "";
    $tempObject->kills = 0;
    $tempObject->daysAlive = 0;
    $tempObject->orderMarker = $i;
    $tempObject->place = 0;
    $tempObject->poisonedDaysCounter = -1; //When someone gets poisoned, the amount of days they have to live is stored here. Is set to -1 when not poisoned.
    $tempObject->typeOfPoison = "";
    array_push($castObject,clone $tempObject);
}
$_SESSION['castObject'] = json_encode($castObject);