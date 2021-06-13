<?php
include('Character.php');
include_once('utils.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="game.css?v=<?= filemtime("game.css") ?>">
    <script
        src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
    crossorigin="anonymous"></script>
    <style>
        *{
            background-color: #87CEEB;
            color: #3D5E6B;
        }
    </style>
    <title>Hunger Games Simulator</title>
</head>
<?php
$SESSION['castObjectToday'] = array_copy($_SESSION['castObject']);
shuffle($_SESSION['castObjectToday']);
$castSize = count($_SESSION['castObjectToday']);
$_SESSION['deadToday'] = [];
$_SESSION['placeToday'] = $_SESSION['place'];

function beginningOfDay(Character $character) {
    $event = '';
    if ($character->poisonedDaysCounter > 0) {
        $character->poisonedDaysCounter--;
        if ($character->poisonedDaysCounter == 0) {
            $event .= $character->nick . " dies of " . $character->typeOfPoison . " poisoning.<br><br>";
            $character->status = "Dead";
            $character->place = $GLOBALS['place']--;
            $character->killedBy = $character->typeOfPoison . " poisoning";
            array_push($_SESSION['deadToday'], $character->nick);
        }
    }
    $character->daysOfWater--;
    if ($character->daysOfWater < 0) {
        $character->strength -= 1.5;
        $character->calculateModifiedStrength();
        if ($character->daysOfWater == -4) {
            $event .= $character->nick . " dies of thirst.<br><br>";
            $character->dead("dehydration");
        }
    } else {
        if (in_array("canteen", $character->inventory)) {
            $character->inventory[array_search("canteen", $character->inventory)] = "empty canteen";
        }
    }
    if ($character->daysOfFood < 0) {
        $character->daysWithoutFood++;
    } else {
        $character->daysOfFood--;
        $character->inventory = removeFromArray("day's worth of rations", $character->inventory);
    }
    if ($character->daysWithoutFood > 1) {
        $character->strength--;
        $character->health -= 0.5;
        $character->calculateModifiedStrength();
        if ($character->health < 0 && $character->status != "Dead") {
            $event .= $character->nick . " starves to death.<br><br>";
            $character->dead("starvation");
        }
    }
    return $event;
}

function sponsor(Character $character) {
    $event = '';
    $sponsorItems = ["an explosive", "some water", "a first aid kit"];
    if (0.0625 * $character->charisma > f_rand()) {
        $randItem = $sponsorItems[rand(0, count($sponsorItems) - 1)];
        $event .= $character->nick . " receives " . $randItem . " from an unknown sponsor.<br><br>" . $character->addItemToInventory($randItem);
        array_push($character->inventory, $randItem);
    }
    return $event;
}

function weightedActionChoice(Character $character) {
    global $castObject;
    $event = '';
    $attackChance = [0.05, 0.2125, 0.4, 0.6125, 0.85];
    $poisonChance = [0.15, 0.31875, 0.5, 0.69375, 0.9];
    if (0.1 > f_rand() && 0.08 * $character->intelligence < f_rand()) {
        $event = $character->triggerTrap();
    } else if ($character->daysOfWater < 2) {
        $event .= $character->lookForWater();
    } else if ($character->daysOfFood < 2) {
        $event .= $character->lookForFood();
    } else if ($character->health < 1.5 && in_array("a first aid kit", $character->inventory) && $character->daysWithoutFood == 0) {
        $event .= $character->heal();
    } else if (in_array("an explosive", $character->inventory) && $character->disposition >= 3 && 0.3 * ($character->disposition - 2) > f_rand() && playersAlive() > 2) {
        $event .= $character->plantExplosive();
    } else if ($character->explosivesPlanted > 0 && playersAlive() > 2) {
        for ($i = 0; $i < $character->explosivesPlanted; $i++) {
            if (playersAlive() >= 5) {
                $numTargets = rand(0, 4);
            } else {
                $numTargets = rand(0, playersAlive() - 1);
            }
            $targets = [];
            if ($numTargets >= 2) {
                $remainingTargets = $_SESSION['castObjectToday'];
                $i = 0;
                while ($i < $numTargets) {
                    $target = $remainingTargets[rand(0, count($remainingTargets) - 1)];
                    if (!(in_array($target, $targets) || $target->status == "Dead" || $target == $character)) {
                        array_push($targets, $target);
                        $remainingTargets = removeFromArray($target, $remainingTargets);
                        $i++;
                    }
                }
                $event .= $character->triggerExplosive($targets);
                foreach ($targets as $target) {
                    $target->actionTaken = true;
                }
                break;
            }
        }
    } else if ($attackChance[$character->disposition - 1] > f_rand()) {
        do {
            $target = $_SESSION['castObjectToday'][round(rand(0, $GLOBALS['castSize'] - 1))];
        } while ($target == $character || $target->status != "Alive");
        $event = $character->attackPlayer($target);
        $target->actionTaken = true;
    } else {
        if (($character->daysOfFood / $character->daysOfWater) * 0.5 > f_rand()) {
            $event .= $character->lookForWater();
        } else {
            $event .= $character->lookForFood();
        }
    }
    $character->actionTaken = true;
    return $event;
}

$events = [];
foreach ($_SESSION['castObjectToday'] as $key => $val) {
    if ($val->status == "Alive") {
        $beginning = beginningOfDay($_SESSION['castObjectToday'][$key]);
        if ($beginning != '') {
            array_push($events, $beginning);
        }
    }
}
if ((int) $_SESSION['counter'] > 1) {
    foreach ($_SESSION['castObjectToday'] as $key => $val) {
        if ($val->status == "Alive") {
            $sponsor = sponsor($_SESSION['castObjectToday'][$key]);
            if ($sponsor != '') {
                array_push($events, $sponsor);
            }
        }
    }
}
foreach ($_SESSION['castObjectToday'] as $key => $val) {
    if (!$val->actionTaken && $val->status == "Alive" && playersAlive($_SESSION['castObjectToday']) > 1) {
        $event = weightedActionChoice($_SESSION['castObjectToday'][$key]);
        if (is_array($event)) {
            $events = array_merge($events, $event);
        } else {
            array_push($events, $event);
        }
    }
    foreach ($_SESSION['castObjectToday'] as $key => $val) {
        if ($val->strength < 0) {
            $_SESSION['castObjectToday'][$key]->strength = 0;
        }
    }
}
?>
<div class="text-center" style="height: 100%">
    <h1>Day <?= $_SESSION['counter'] ?></h1>
    <?php
    showEvents($events, $_SESSION['castObjectToday']);
    $nextDestination = 'deadTributes.php';
    foreach ($_SESSION['castObjectToday'] as $key => $val) {
        $_SESSION['castObjectToday'][$key]->actionTaken = false;
    }
    if (playersAlive() == 1) {
        foreach ($_SESSION['castObjectToday'] as $key => $val) {
            if ($val->status == "Alive") {
                $_SESSION['castObjectToday'][$key]->place = 1;
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
        window.location = "<?= $nextDestination; ?>";
    }
</script>