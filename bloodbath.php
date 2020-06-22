<?php
include('Character.php');
include_once('utils.php');
session_start();
?> 
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="bloodbath.css?v=<?= filemtime("bloodbath.css") ?>">
<script
    src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
crossorigin="anonymous"></script>
<title>Hunger Games Simulator</title>
<?php
$_SESSION['castObjectToday'] = array_copy($_SESSION['castObject']);
//print_r2($_SESSION['castObjectToday']);
shuffle($_SESSION['castObjectToday']);
$castSize = count($_SESSION['castObjectToday']);
$_SESSION['deadToday'] = [];
$_SESSION['placeToday'] = $_SESSION['place'];

function avg_strength($array) {
    $strengths = [];
    foreach ($array as $value) {
        array_push($strengths, $value->modifiedStrength);
    }
    return array_sum($strengths) / count($strengths);
}

function compareItems($items) { //This function loops through all the items and evaluates who gets what.
    $events = [];
    for ($i = 0; $i < count($items); $i++) { //Go through all items available.
        $fightArray = [];
        $otherFighters = [];
        foreach ($_SESSION['castObjectToday'] as $character) {
            if (in_array($i, $character->desiredItems)) {
                array_push($fightArray, $character);
            }
        }
        if (count($fightArray) == 0) { //If no one wants this item, move on.
            continue;
        } else if (count($fightArray) == 1) { //If only one person wants this item, give it to them.
            unset($fightArray[0]->desiredItems[array_search($i, $fightArray[0]->desiredItems)]);
            array_push($fightArray[0]->inventory, $items[$i]);
            array_push($events, $fightArray[0]->nick . " grabs " . ((in_array(substr($items[$i], 0, 1), ["a", "e", "i", "o"])) ? "an " : "a ") . $items[$i] . ".<br><br>" . $fightArray[0]->addItemToInventory($items[$i], true));
            $fightArray[0]->calculateModifiedStrength();
        } else {
            $characterAttackLottery = [];
            foreach ($fightArray as $fighter) {
                for ($j = 0; $j < ($fighter->modifiedStrength * 100 + $fighter->intelligence * 100); $j++) {
                    array_push($characterAttackLottery, $fighter);
                }
            }
            shuffle($characterAttackLottery);
            $strongestCharacter = $characterAttackLottery[rand(0, count($characterAttackLottery) - 1)];
            $otherFighters = removeFromArray($strongestCharacter, $fightArray);
            //print_r2($otherFighters);
            foreach ($fightArray as $fighter) {
                $fighter->strength -= (avg_strength(removeFromArray($fighter, $fightArray)) - $fighter->defense) * ((count($otherFighters) == 1) ? 1 : 0.75);
                $fighter->health -= (avg_strength(removeFromArray($fighter, $fightArray)) - $fighter->defense) * ((count($otherFighters) == 1) ? 1 : 0.75);
                $fighter->calculateModifiedStrength();
            }
            $strongestCharacter->desiredItems = removeFromArray($i, $strongestCharacter->desiredItems);
            array_push($strongestCharacter->inventory, $items[$i]);
            array_push($events, $strongestCharacter->nick . " attacks " . nameList($otherFighters) . (($strongestCharacter->equippedItem != "") ? " with " . $strongestCharacter->equippedItem : "") . " and steals the " . $items[$i] . " that they were " . (count($fightArray) == 2 ? "both" : "all") . " fighting over.<br><br>" . $strongestCharacter->addItemToInventory($items[$i], true));
        }
        $deadNow = 0;
        foreach ($fightArray as $fighter) {
            if ($fighter->health < 0 && $fighter->status == "Alive") {
                $fighter->dead(nameList(removeFromArray($fighter, $fightArray)), false);
                array_push($events, $fighter->nick . " succumbs to " . (($fighter->gender == "m") ? "his" : "her") . " injuries and dies.<br><br>");
                $deadNow++;
                $fighter->desiredItems = [];
                $strongestCharacter->kill($fighter);
                foreach (removeFromArray($fighter, $fightArray) as $victor) {
                    if($victor != $strongestCharacter){
                        $victor->kills++;
                    }
                }
            }
        }
        $_SESSION['placeToday'] -= $deadNow;
    }
    return $events;
}

function initializeItems() {
    global $castSize;
    $items = [];
    for ($i = 0; $i < 2 * $castSize; $i++) {
        array_push($items, "day's worth of rations");
        array_push($items, "canteen");
    }
    for ($i = 0; $i < round(0.9 * $castSize); $i++) {
        array_push($items, "knife");
        array_push($items, "bow and quiver");
    }
    for ($i = 0; $i < round(0.35 * $castSize); $i++) {
        array_push($items, "backpack");
    }
    for ($i = 0; $i < round(0.15 * $castSize); $i++) {
        array_push($items, "mace");
        array_push($items, "axe");
    }
    shuffle($items);
    return $items;
}

$items = initializeItems();
$events = [];
foreach ($_SESSION['castObjectToday'] as $character) {
    if ($character->intelligence <= 3 && 1 - ($character->intelligence * 0.025) < f_rand()) {
        array_push($events, $character->nick . " steps off " . (($character->gender == "m") ? "his" : "her") . " podium too early and explodes.<br><br>");
        $character->dead((($character->gender == "m") ? "his" : "her") . "podium");
        $character->desiredItems = [];
    }
    $runawayChance = [0.8, 0.65, 0.35, 0.15, 0.05];
    if (1 - $runawayChance[$character->disposition - 1] < f_rand() && $character->status == "Alive") {
        array_push($events, $character->nick . " runs away from the Cornucopia.<br><br>");
    } else {
        for ($i = 0; $i < round(f_rand(1.5, 1.75) * $character->disposition); $i++) {
            if ($character->status == "Alive") {
                array_push($character->desiredItems, round(rand(0, count($items) - 1)));
            }
        }
    }
}
$events = array_merge($events, compareItems($items));
?>
<div class="text-center" style="height:100%">
    <h1>Bloodbath</h1>
    <?php
    showEvents($events);
    ?>
    <button class="btn btn-primary" onclick="next()">Continue</button>
</div>
<script>
    function next() {
        $.ajax({
            url: "editVariables.php",
            async: false,
            method: "POST",
            dataType: "text",
            success: function (response) {
                console.log(response);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
        window.location = "day.php";
    }
</script>
