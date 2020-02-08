<?php
session_start();
?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="night.css?v=<?= filemtime("night.css") ?>">
<script
    src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
crossorigin="anonymous"></script>
<title>Hunger Games Simulator</title>
<body style="background-color:#000">
    <div class="text-center" style="height: 100%">
        <h1>Night <?= $_COOKIE['counter'] ?></h1>
        <?php
//setcookie("counter", ((int) $_COOKIE['counter']) + 1, 0, "/");
        $castObject = json_decode($_SESSION['castObject']);
        shuffle($castObject);
        //print_r2($castObject);
        $castSize = count($castObject);
        $deadToday = json_decode($_COOKIE['deadToday']);
        $place = (int) $_COOKIE['place'];

        function f_rand($min = 0, $max = 1, $mul = 1000000) {
            if ($min > $max)
                return false;
            return mt_rand($min * $mul, $max * $mul) / $mul;
        }

        function removeFromArray($value, $array) {
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
            $character->modifiedStrength = calculateModifiedStrength($character);
            return $events;
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

        function showEvents($events) {
            global $castObject;
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
            global $castObject;
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

        function goToSleep($character) {
            $character->status = "Asleep";
            return $character->nick . " decides to go to sleep.<br><br>";
        }

        function attackPlayer($character, $target) {
            $event = '';
            $event .= $character->nick . (($target->status != "Asleep") ? " attempts to attack " : " attacks ") . $target->nick . (($character->equippedItem != "") ? " with " . $character->equippedItem : "") . (($target->status == "Asleep") ? " while " . (($target->gender == "m") ? "he" : "she") . " is sleeping" : "") . ".<br><br>";
            if (($target->status == "Asleep") ? FALSE : 0.04 * $character->dexterity + 0.7 < f_rand() || 0.04 * $target->dexterity + 0.3 > f_rand()) {
                $event .= "However, it does not connect.<br><br>";
                if (0.3 * ($target->disposition - 2) > f_rand()) {
                    $target->status = "Alive";
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
                            foreach ($character->inventory as $item) {
                                $event .= addItemToInventory($item, $target);
                            }
                        }
                    }
                }
            } else {
                if ($target->status != "Asleep") {
                    $event .= (($character->gender == "m") ? "He" : "She") . " makes a successful attack.<br><br>";
                }
                $target->strength -= $character->modifiedStrength - $target->defense;
                $target->health -= $character->modifiedStrength - $target->defense;
                $target->status = "Alive";
            }
            if ($target->health < 0) {
                $character->kills++;
                $target->killedBy = $character->nick;
                array_merge($character->inventory, $target->inventory);
                foreach ($target->inventory as $item) {
                    $event .= addItemToInventory($item, $character);
                }
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
            $character->inventory = removeFromArray("a first aid kit", $character->inventory);
            return $event;
        }

        function action($character) {
            global $castObject;
            $event = '';
            $chosenAction = weightedActionChoice($character);
            if ($chosenAction == "go to sleep") {
                $event .= goToSleep($character);
                $character->actionTaken = "true";
            } else if ($chosenAction == "attack another player") {
                do {
                    $target = $castObject[round(rand(0, count($castObject) - 1))];
                    //$target = $castObject[0];
                } while ($target == $character || $target->status == "Dead");
                $event .= attackPlayer($character, $target);
                $character->actionTaken = "true";
                $target->actionTaken = "true";
            } else if ($chosenAction == "heal") {
                $event .= heal($character);
                $character->actionTaken = "true";
            }

            return $event;
        }

        function weightedActionChoice($character) {
            $attackChance = [0.05, 0.15, 0.35, 0.65, 0.85];
            if ($character->strength < 1.5 && in_array("a first aid kit", $character->inventory)) {
                return "heal";
            } else if ($attackChance[$character->disposition - 1] > f_rand() && $character->equippedItem != "") {
                return "attack another player";
            } else {
                return "go to sleep";
            }
        }

        $events = [];
        foreach ($GLOBALS['castObject'] as $character) {
            if ($character->actionTaken == "false" && $character->status != "Dead") {
                array_push($events, action($character));
            }
            $deadNow = 0;
            foreach ($GLOBALS['castObject'] as $fighter) {
                if ($fighter->health < 0 && $fighter->status != "Dead") {
                    array_push($events, $fighter->nick . " succumbs to " . (($fighter->gender == "m") ? "his" : "her") . " injuries and dies.<br><br>");
                    $fighter->status = "Dead";
                    $fighter->place = $GLOBALS['place'];
                    $deadNow++;
                    array_push($GLOBALS['deadToday'], $fighter->nick);
                }
                if ($fighter->strength < 0) {
                    $fighter->strength = 0;
                }
            }
            $GLOBALS['place'] -= $deadNow;
            //setcookie("deadToday", json_encode($GLOBALS['deadToday']), 0, "/");
        }
        //print_r2($events);
        //print_r2($castObject);
        showEvents($events);
        $playersAlive = 0;
        $nextDestination = 'day.php';
        foreach ($castObject as $character) {
            $character->actionTaken = "false";
            if ($character->status != "Dead") {
                $character->status = "Alive";
                $playersAlive++;
                $character->daysAlive++;
            }
        }
        if ($playersAlive == 1) {
            foreach ($castObject as $character) {
                if ($character->status == "Alive") {
                    $character->place = 1;
                }
            }
            $nextDestination = 'winner.php';
        }
        ?>
        <button class="btn btn-primary" onclick="next()">Continue</button>
    </div>
</body>
<script>
    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }
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
        document.cookie = "counter=" + String(parseInt(getCookie("counter")) + 1);
        window.location = "<?php echo $nextDestination; ?>";
    }
</script>