<?php
include('Character.php');
include_once('utils.php');
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="night.css?v=<?= filemtime("night.css") ?>">
<script
    src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
crossorigin="anonymous"></script>
<title>Hunger Games Simulator</title>
<?php
//setcookie("counter", ((int) $_COOKIE['counter']) + 1, 0, "/");
$SESSION['castObjectToday'] = array_copy($_SESSION['castObject']);
shuffle($_SESSION['castObjectToday']);
$castSize = count($_SESSION['castObjectToday']);
$_SESSION['deadToday'] = [];
$_SESSION['placeToday'] = $_SESSION['place'];

function weightedActionChoice(Character $character) {
    $event = '';
    $attackChance = [0.05, 0.15, 0.35, 0.65, 0.85];
    if ($character->strength < 1.5 && in_array("a first aid kit", $character->inventory)) {
        $event .= $character->heal();
    } else if ($attackChance[$character->disposition - 1] > f_rand() && $character->equippedItem != "") {
        do {
            $target = $_SESSION['castObjectToday'][round(rand(0, $GLOBALS['castSize'] - 1))];
        } while ($target == $character || $target->status == "Dead");
        $event = $character->attackPlayer($target);
        $target->actionTaken = "true";
    } else {
        $event .= $character->goToSleep();
        print_r2($character->status);
    }
    $character->actionTaken = "true";
    return $event;
}
unset($character);
$events = [];
foreach ($_SESSION['castObjectToday'] as $castMember) {
    if ($castMember->actionTaken == "false" && $castMember->status != "Dead" && playersAlive() > 1) {
        $event = weightedActionChoice($castMember);
        if (is_array($event)) {
            $events = array_merge($events, $event);
        } else {
            array_push($events, $event);
        }
    }
    unset($castMember);
}
foreach ($_SESSION['castObjectToday'] as $fighter) {
    if ($fighter->strength < 0) {
        $fighter->strength = 0;
    }

    print_r2($fighter->status);
}
?>
<div class="text-center" style="height: 100%">
    <h1>Night <?= $_SESSION['counter'] ?></h1>
    <?php
    showEvents($events);
    $nextDestination = 'day.php';
    foreach ($_SESSION['castObjectToday'] as $character) {
        $character->actionTaken = "false";
        if ($character->status != "Dead") {
            $character->status = "Alive";
            $character->daysAlive++;
        }
    }
    unset($character);
    if (playersAlive() == 1) {
        foreach ($_SESSION['castObjectToday'] as $character) {
            if ($character->status == "Alive") {
                $character->place = 1;
            }
        }
        $nextDestination = 'winner.php';
    }
    ?>
    <button class="btn btn-primary" onclick="next()">Continue</button>
</div>
<script>
    function next() {
        $.ajax({
            url: "editVariables.php",
            async: false,
            method: "POST",
            data: "counter=" + <?php echo $_SESSION['counter'] + 1 ?>,
            dataType: "text",
            success: function (response) {
                console.log(response);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
        window.location = "<?php echo $nextDestination; ?>";
    }
</script>