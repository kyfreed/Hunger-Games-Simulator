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
$_SESSION['castObjectToday'] = array_copy($_SESSION['castObject']);
shuffle($_SESSION['castObjectToday']);
$castSize = count($_SESSION['castObjectToday']);
$_SESSION['deadToday'] = [];
$_SESSION['placeToday'] = $_SESSION['place'];

function weightedActionChoice($key) {
    $event = '';
    $attackChance = [0.05, 0.15, 0.35, 0.65, 0.85];
    if ($_SESSION['castObjectToday'][$key]->strength < 1.5 && in_array("a first aid kit", $_SESSION['castObjectToday'][$key]->inventory)) {
        $event .= $_SESSION['castObjectToday'][$key]->heal();
    } else if ($attackChance[$_SESSION['castObjectToday'][$key]->disposition - 1] > f_rand() && $_SESSION['castObjectToday'][$key]->equippedItem != "") {
        do {
            $target = $_SESSION['castObjectToday'][round(rand(0, $GLOBALS['castSize'] - 1))];
        } while ($target == $_SESSION['castObjectToday'][$key] || $target->status == "Dead");
        $event = $_SESSION['castObjectToday'][$key]->attackPlayer($target);
        $target->actionTaken = "true";
    } else {
        $event .= $_SESSION['castObjectToday'][$key]->goToSleep();
        //print_r2($character->status);
    }
    $_SESSION['castObjectToday'][$key]->actionTaken = "true";
    return $event;
}
$events = [];
foreach ($_SESSION['castObjectToday'] as $key => $val) {
    if(isset($oldKey)){
        print_r2("Status of previous iteration: " . $_SESSION['castObjectToday'][$oldKey]->status);
    }
    if ($val->actionTaken == "false" && $val->status != "Dead" && playersAlive($_SESSION['castObjectToday']) > 1) {
        print_r2("Before: $val->nick " . $_SESSION['castObjectToday'][$key]->status);
        $event = weightedActionChoice($key);
        print_r2("After: $val->nick " . $_SESSION['castObjectToday'][$key]->status);
        if (is_array($event)) {
            $events = array_merge($events, $event);
        } else {
            array_push($events, $event);
        }
    }
    $oldKey = $key;
}
foreach ($_SESSION['castObjectToday'] as $key => $val) {
   print_r2("Outside of loop: $val->nick " . $_SESSION['castObjectToday'][$key]->status);
    if ($val->strength < 0) {
        $_SESSION['castObjectToday'][$key]->strength = 0;
    }
}
?>
<div class="text-center" style="height: 100%">
    <h1>Night <?= $_SESSION['counter'] ?></h1>
    <?php
    showEvents($events);
    $nextDestination = 'day.php';
    foreach ($_SESSION['castObjectToday'] as $key => $val) {
        $_SESSION['castObjectToday'][$key]->actionTaken = "false";
        if ($val->status != "Dead") {
            $_SESSION['castObjectToday'][$key]->status = "Alive";
            $_SESSION['castObjectToday'][$key]->daysAlive++;
        }
    }
    unset($character);
    if (playersAlive($_SESSION['castObjectToday']) == 1) {
        foreach ($_SESSION['castObjectToday'] as $key => $val) {
            if ($val->status == "Alive") {
                $_SESSION['castObjectToday'][$key]->place = 1;
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