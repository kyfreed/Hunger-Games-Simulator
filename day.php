<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="day.css?v=<?= filemtime("day.css") ?>">
<script
    src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
crossorigin="anonymous"></script>
<title>Hunger Games Simulator</title>
<?php
$castObject = json_decode($_SESSION['castObject']);
shuffle($castObject);
$castSize = count($castObject);
$deadToday = json_decode($_COOKIE['deadToday']);
$place = (int) $_COOKIE['place'];

function beginningOfDay($character) {
    $event = '';
    if ($character->daysOfFood < 0) {
        $character->daysWithoutFood++;
    } else {
        $character->daysOfFood--;
        $character->inventory = removeFromArray("day's worth of rations", $character->inventory);
    }
    if ($character->daysWithoutFood > 1) {
        $character->strength--;
        $character->health -= 0.5;
        $character->modifiedStrength = calculateModifiedStrength($character);
        if ($character->health < 0) {
            $event .= $character->nick . " starves to death.<br><br>";
            $character->status = "Dead";
            $character->place = $GLOBALS['place'] --;
            $character->killedBy = "starvation";
            array_push($GLOBALS['deadToday'], $character->nick);
        }
    }
    $character->daysOfWater--;
    if ($character->daysOfWater < 0) {
        $character->strength -= 1.5;
        $character->modifiedStrength = calculateModifiedStrength($character);
        if ($character->daysOfWater == -4) {
            $event .= $character->nick . " dies of thirst.<br><br>";
            $character->status = "Dead";
            $character->place = $GLOBALS['place'] --;
            $character->killedBy = "dehydration";
            array_push($GLOBALS['deadToday'], $character->nick);
        }
    } else {
        if (in_array("canteen", $character->inventory)) {
            $character->inventory[array_search("canteen", $character->inventory)] = "empty canteen";
        }
    }
    return $event;
}

function sponsor($character) {
    $event = '';
    $sponsorItems = ["an explosive", "some water", "a first aid kit"];
    if (0.0625 * $character->charisma > f_rand()) {
        $randItem = $sponsorItems[rand(0, count($sponsorItems) - 1)];
        $event .= $character->nick . " receives " . $randItem . " from an unknown sponsor.<br><br>" . addItemToInventory($randItem, $character);
        array_push($character->inventory, $randItem);
    }
    return $event;
}

function weightedActionChoice($character) {
    global $castObject;
    $event = '';
    $attackChance = [0.05, 0.2125, 0.4, 0.6125, 0.85];
    $poisonChance = [0.15, 0.31875, 0.5, 0.69375, 0.9];
    if (0.1 > f_rand() && 0.08 * $character->intelligence < f_rand()) {
        $event .= triggerTrap($character);
    } else if ($character->daysOfWater < 2) {
        $event .= lookForWater($character);
    } else if ($character->daysOfFood < 2) {
        $event .= lookForFood($character);
    } else if ($character->strength < $character->maxStrength * 0.3 && in_array("a first aid kit", $character->inventory)) {
        $event .= heal($character);
    } else if (in_array("an explosive", $character->inventory) && $character->disposition >= 3 && 0.3 * ($character->disposition - 2) > f_rand()) {
        $event .= plantExplosive($character);
    } else if ($character->explosivesPlanted > 0) {
        $numTargets = rand(0, 4);
        $targets = [];
        if ($numTargets >= 2) {
            $remainingTargets = $castObject;
            $i = 0;
            while ($i < (int) substr($chosenAction, -1)) {
                $target = $remainingTargets[rand(0, count($remainingTargets) - 1)];
                if (!(in_array($target, $targets) || $target->status == "Dead" || $target == $character)) {
                    array_push($targets, $target);
                    $remainingTargets = removeFromArray($target, $remainingTargets);
                    $i++;
                }
            }
            $event .= triggerExplosive($character, $targets);
            foreach ($targets as $target) {
                $target->actionTaken = "true";
            }
        }
    } else if ($attackChance[$character->disposition - 1] > f_rand()) {
        do {
            $target = $castObject[round(rand(0, count($castObject) - 1))];
        } while ($target == $character || $target->status != "Alive");
        $event .= attackPlayer($character, $target);
        $target->actionTaken = "true";
    } else {
        if (($character->daysOfFood / $character->daysOfWater) * 0.5 > f_rand()) {
            $event .= lookForWater($character);
        } else {
            $event .= lookForFood($character);
        }
    }
    $character->actionTaken = true;
    return $event;
}

function showEvents($events) {
    foreach ($events as $event) {
        foreach (getCharacterByEvent($event) as $character) {
            ?>
            <img src="<?= $character->image ?>" width="90" height="90"/>
            <?php
        }
        ?>
        <br>
        <?php
        echo $event;
    }
}

function calculateModifiedStrength($character) {
    $modStr = 0;

    if (in_array("axe", $character->inventory) || in_array("mace", $character->inventory)) {
        $modStr = $character->strength + 5;
        $character->equippedItem = "an axe";
    } else if ($character->strength < 2.4 && in_array("a knife", $character->inventory) || in_array("knife", $character->inventory)) {
        $knives = 0;
        foreach ($character->inventory as $value) {
            if (strpos("knife", $value) !== false) {
                $knives++;
            }
        }
        if ($knives > 1) {
            $modStr = 4.8;
            $character->equippedItem = "two knives";
        } else {
            $modStr = 2.4;
            $character->equippedItem = "a knife";
        }
    } else {
        $modStr = $character->strength / 5;
        $character->equippedItem = "";
    }
    return $modStr;
}

function getCharacterByEvent($event) {
    global $castObject;
    $characterArray = [];
    foreach ($castObject as $character) {
        if (strpos($event, $character->nick . " ") !== FALSE || strpos($event, $character->nick . ".") !== FALSE || strpos($event, $character->nick . ",") !== FALSE) {
            if (count($characterArray) == 0 || firstAfter($character->nick, $characterArray, $event) == -1) {
                array_push($characterArray, $character);
            } else {
                array_splice($characterArray, firstAfter($character->nick, $characterArray, $event), 0, array($character));
            }
        }
    }
    return $characterArray;
}

function firstAfter($sub, $array, $string) {
    $index = -1;
    $strPosAfter = strlen($string);
    for ($i = 0; $i < count($array); $i++) {
        if (strpos($string, $sub) < strpos($string, $array[$i]->nick) && strpos($string, $array[$i]->nick) < $strPosAfter) {
            $index = $i;
            $strPosAfter = strpos($string, $array[$i]->nick);
        }
    }
    return $index;
}

function lookForWater($character) {
    $event = $character->nick . " goes searching for water.<br><br>" . (($character->gender == "m") ? "He" : "She");
    if ((0.05 * $character->intelligence) + 0.6 > f_rand()) {
        $character->daysOfWater++;
        $character->daysWithoutWater = 0;
        $event .= " finds a water source and drinks from it.<br><br>";
        if (in_array("empty canteen", $character->inventory)) {
            $canteens = array_count_values($character->inventory)["empty canteen"];
            $event .= (($character->gender == "m") ? "He" : "She") . " fills " . (($character->gender == "m") ? "his" : "her") . " canteen" . (($canteens == 1) ? "" : "s") . ".<br><br>";
            $character->daysOfWater += $canteens;
            for ($i = 0; $i < count($character->inventory); $i++) {
                if ($character->inventory[$i] == "empty canteen") {
                    $character->inventory[$i] = "canteen";
                }
            }
        }
        if (in_array("fishing gear", $character->inventory)) {
            $catchResults = floor((($character->dexterity * $character->intelligence) / 2) * f_rand(0.15, 0.45));
            $event .= (($character->gender == "m") ? "He" : "She") . " also fishes and gains " . (($catchResults > 0) ? $catchResults . (($catchResults == 1) ? " day's" : " days'") . " worth of food.<br><br>" : "nothing.<br><br>");
        }
    } else {
        $event .= " doesn't find any.<br><br>";
    }
    return $event;
}

function lookForFood($character) {
    $event = $character->nick . " goes searching for food.<br><br>" . (($character->gender == "m") ? "He" : "She");
    $shootChance = f_rand();
    if (in_array("bow and quiver", $character->inventory) && $character->arrows > 0) {
        $event .= " attempts to shoot a wild animal.<br><br>" . (($character->gender == "m") ? "He" : "She");
        if (0.12 * $character->dexterity > $shootChance) {
            $foodGain = rand(2, 5);
            $event .= " is successful. " . (($character->gender == "m") ? "He" : "She") . " gains " . $foodGain . " days' worth of food.<br><br>";
            $character->daysOfFood += $foodGain;
            for ($i = 0; $i < $foodGain; $i++) {
                array_push($character->inventory, "a day's worth of rations");
            }
            $character->daysWithoutFood = 0;
        } else {
            $event .= " misses.<br><br>";
        }
    } else {
        if ((0.05 * $character->intelligence) + 0.5 > f_rand()) {
            $character->daysOfFood++;
            array_push($character->inventory, "a day's worth of rations");
            $character->daysWithoutFood = 0;
            $event .= " finds some wild fruit and gains a day's worth of food.<br><br>";
        } else {
            $event .= " doesn't find any.<br><br>";
        }
    }
    return $event;
}

function attackPlayer($character, $target) {
    $event = '';
    if (in_array("bow and quiver", $character->inventory) && $character->arrows > 0 && $character->strength <= 2.4) {
        $event .= $character->nick . " attempts to shoot " . $target->nick . " with an arrow.<br><br>";
        $character->arrows--;
        if ($character->dexterity * 0.12 > f_rand()) {
            $event .= "A direct hit!<br><br>";
            $target->strength -= round(f_rand(0.75, 1.75), 2);
        } else {
            $event .= "However, the arrow misses.<br><br>";
        }
    } else {
        $event .= $character->nick . " attempts to attack " . $target->nick . (($character->equippedItem != "") ? " with " . $character->equippedItem : "") . ".<br><br>";
        if (0.04 * $character->dexterity + 0.7 < f_rand() || 0.04 * $target->dexterity + 0.3 > f_rand()) {
            $event .= "However, it does not connect.<br><br>";
            if (0.3 * ($target->disposition - 2) > f_rand()) {
                $event .= $target->nick . " prepares to retaliate!<br><br>";
                if (0.04 * $target->dexterity + 0.75 < f_rand() || 0.04 * $character->dexterity + 0.25 > f_rand()) {
                    $event .= "Unfortunately, this fails as well.<br><br>";
                } else {
                    $event .= (($target->gender == "m") ? "He" : "She") . " is successful in doing so.<br><br>";
                    $character->strength -= $target->modifiedStrength - $character->defense;
                    $character->health -= $target->modifiedStrength - $character->defense;
                    if ($character->health < 0) {
                        $target->kills++;
                        $character->killedBy = $target->nick;
                        array_merge($target->inventory, $character->inventory);
                        $target->arrows += $character->arrows;
                        foreach ($character->inventory as $item) {
                            if (!($item == "bow and quiver")) {
                                $event .= addItemToInventory($item, $target);
                            }
                        }
                    }
                }
            }
        } else {
            $event .= (($character->gender == "m") ? "He" : "She") . " makes a successful attack.<br><br>";
            $target->strength -= $character->modifiedStrength - $target->defense;
            $target->health -= $character->modifiedStrength - $target->defense;
        }
    }
    if ($target->health < 0) {
        $character->kills++;
        $target->killedBy = $character->nick;
        array_merge($character->inventory, $target->inventory);
        $character->arrows += $target->arrows;
        foreach ($target->inventory as $item) {
            if (!($item == "bow and quiver")) {
                $event .= addItemToInventory($item, $character);
            }
        }
    }
    return $event;
}

function plantExplosive($character) {
    $character->explosivesPlanted++;
    $character->inventory = removeFromArray("an explosive", $character->inventory);
    return $character->nick . " plants an explosive.<br><br>";
}

function triggerExplosive($character, $targets) {
    $character->explosivesPlanted--;
    foreach ($targets as $target) {
        $target->status = "Dead";
        $target->place = $GLOBALS['place'];
        $target->killedBy = $character->nick . "'s explosive";
        array_merge($character->inventory, $target->inventory);
        $character->arrows += $target->arrows;
        foreach ($target->inventory as $item) {
            if (!($item == "bow and quiver")) {
                addItemToInventory($item, $character);
            }
        }
        array_push($GLOBALS['deadToday'], $target->nick);
    }
    $GLOBALS['place'] -= count($targets);
    $character->kills += count($targets);
    return $character->nick . " sets off an explosive, killing " . nameList($targets) . ".<br><br>";
}

function triggerTrap($character) {
    $event = '';
    $event .= $character->nick . " steps on a bear trap.<br><br>";
    $character->strength -= 3;
    $character->health -= 3;
    if ($character->health < 0) {
        $character->killedBy = "a bear trap";
    }
    return $event;
}

function heal($character) {
    $event = '';
    $event .= $character->nick . " tends to " . (($character->gender == "m") ? "his" : "her") . " injuries.<br><br>";
    if ($character->strength + 1 <= $character->maxStrength) {
        $character->strength += 1;
    } else {
        $character->strength = $character->maxStrength;
    }
    if ($character->health + 1 <= $character->maxStrength) {
        $character->health += 1;
    } else {
        $character->health = $character->maxStrength;
    }
    $character->inventory = removeFromArray("a first aid kit", $character->inventory);
    return $event;
}

function nameList($array) {
    if (count($array) == 1) {
        return $array[0]->nick;
    } else if (count($array) == 2) {
        return $array[0]->nick . " and " . $array[1]->nick;
    } else {
        $listString = '';
        for ($i = 0; $i < count($array) - 1; $i++) {
            $listString .= $array[$i]->nick . ", ";
        }
        $listString .= "and " . end($array)->nick;
        return $listString;
    }
}

function addItemToInventory($item, $character) {
    $events = '';
    if ($item == "day's worth of rations") {
        $character->daysOfFood++;
    }
    if ($item == "canteen" || $item == "some water") {
        $character->daysOfWater++;
    }
    if ($item == "bow and quiver") {
        $character->arrows += 20;
    }
    if ($item == "poison") {
        for ($i = 0; $i < 3; $i++) {
            array_push($character->inventory, "dose of poison");
        }
    }
    while (in_array("poison", $character->inventory)) {
        $character->inventory = removeFromArray("poison", $character->inventory);
    }

    $character->modifiedStrength = calculateModifiedStrength($character);
    return $events;
}

function f_rand($min = 0, $max = 1, $mul = 1000000) {
    if ($min > $max)
        return false;
    return mt_rand($min * $mul, $max * $mul) / $mul;
}

function startsWith($haystack, $needle) {
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle) {
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

function removeFromArray($value, $array) {
    if (!in_array($value, $array)) {
        return $array;
    }
    $newArray = $array;
    unset($newArray[array_search($value, $newArray)]);
    $newArray = array_values($newArray);
    return $newArray;
}

function print_r2($val) { //Prints an object to the page in a readable format.
    echo '<pre>';
    print_r($val);
    echo '</pre>';
}

function playersAlive() {
    global $castObject;
    $alive = 0;
    foreach ($castObject as $character) {
        if ($character->status != "Dead") {
            $character->status = "Alive";
            $alive++;
        }
    }
    return $alive;
}

$events = [];
foreach ($GLOBALS['castObject'] as $character) {
    if ($character->status == "Alive") {
        $beginning = beginningOfDay($character);
        if ($beginning != '') {
            array_push($events, $beginning);
        }
    }
}
if ((int) $_COOKIE['counter'] > 1) {
    foreach ($GLOBALS['castObject'] as $character) {
        if ($character->status == "Alive") {
            $sponsor = sponsor($character);
            if ($sponsor != '') {
                array_push($events, $sponsor);
            }
        }
    }
}
foreach ($GLOBALS['castObject'] as $character) {
    if ($character->actionTaken == "false" && $character->status == "Alive" && playersAlive() > 1) {
        array_push($events, weightedActionChoice($character));
    }
    $deadNow = 0;
    foreach ($GLOBALS['castObject'] as $fighter) {
        if ($fighter->health < 0 && $fighter->status == "Alive") {
            array_push($events, $fighter->nick . " succumbs to " . (($fighter->gender == "m") ? "his" : "her") . " injuries and dies.<br><br>");
            $fighter->status = "Dead";
            $deadNow++;
            $fighter->place = $GLOBALS['place'];
            array_push($GLOBALS['deadToday'], $fighter->nick);
        }
        if ($fighter->strength < 0) {
            $fighter->strength = 0;
        }
    }
    $GLOBALS['place'] -= $deadNow;
}
?>
<div class="text-center" style="height: 100%">
    <h1>Day <?= $_COOKIE['counter'] ?></h1>
    <?php
    showEvents($events);
    $nextDestination = 'deadTributes.php';
    foreach ($castObject as $character) {
        $character->actionTaken = "false";
    }
    if (playersAlive() == 1) {
        foreach ($castObject as $character) {
            if ($character->status == "Alive") {
                $character->place = 1;
            }
        }
        $nextDestination = 'winner.php';
    }
    ?>
    <div class="text-center">
        <button class="btn btn-primary" onclick="next()">Continue</button>
    </div>
</div>
<script>
    function next() {
        $.ajax({
            url: "editFile.php",
            async: false,
            method: "POST",
            data: JSON.stringify(<?= json_encode($castObject) ?>),
            contentType: "text/plain",
            dataType: "text",
            success: function (response) {
                console.log(response);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
        document.cookie = "deadToday=" + '<?php echo json_encode($GLOBALS['deadToday']) ?>';
        document.cookie = "place=" + <?php echo $GLOBALS['place'] ?>;
        window.location = "<?php echo $nextDestination; ?>";
    }
</script>