<?php

session_start();
include_once('utils.php');

class Character {

    public $name; //Name used on start and stats screens.
    public $nick; //Name used in the main game.
    public $gender;
    public $disposition; //Basically aggression. I would have changed the name but I didn't want to search for all its uses.
    public $strength;
    public $health;
    public $maxStrength;
    public $maxHealth;
    public $modifiedStrength; //A converted value that interprets the inputted strength value into a value that makes sense in game logic.
    public $dexterity; //Dexterity determines probability of successful attacks and dodges.
    public $intelligence; //Intelligence determines probability of finding food or water, avoiding bear traps, and detecting poison.
    public $charisma; //Charisma determines probability of being sponsored and characters allying with them.
    public $defense = 0;
    public $image;
    public $status = "Alive";
    public $actionTaken = "false";
    public $daysOfFood = 1; //daysofFood and daysofWater are set to 1 so that characters don't immediately begin starving after the Bloodbath.
    public $daysWithoutFood = 0;
    public $daysOfWater = 1;
    public $desiredItems = [];
    public $inventory = [];
    public $arrows = 0;
    public $explosivesPlanted = 0;
    public $memberOfAlliance = -1;
    public $equippedItem = "";
    public $kills = 0;
    public $daysAlive = 0;
    public $orderMarker;
    public $place = 0;
    public $poisonedDaysCounter = -1; //When someone gets poisoned, the amount of days they have to live is stored here. Is set to -1 when not poisoned.
    public $typeOfPoison = "";
    public $killedBy = "";

    function __construct($name, $nick, $gender, $disposition, $strength, $health, $dexterity, $intelligence, $charisma, $image, $orderMarker) {
        $this->name = htmlspecialchars($name);
        $this->nick = $nick;
        $this->gender = $gender;
        $this->disposition = $disposition;
        $this->strength = $strength;
        $this->maxStrength = $strength;
        $this->health = $health;
        $this->maxHealth = $health;
        $this->modifiedStrength = $strength / 5;
        $this->dexterity = $dexterity;
        $this->intelligence = $intelligence;
        $this->charisma = $charisma;
        $this->image = $image;
        $this->orderMarker = $orderMarker;
    }

    function die($cause = null, $decrementPlace = true) {
        $this->status = "Dead";
        $this->place = $_SESSION['placeToday'];
        if ($decrementPlace) {
            $_SESSION['placeToday'] --;
        }
        if (!(is_null($cause))) {
            $this->killedBy = $cause;
        }
        array_push($_SESSION['deadToday'], $this->nick);
    }

    function kill(Character $target) {
        if ($target->killedBy == "") {
            $target->killedBy = $this->nick;
        }
        $this->inventory = array_merge($this->inventory, $target->inventory);
        $this->arrows += $target->arrows;
        foreach ($target->inventory as $item) {
            if (!($item == "bow and quiver")) {
                $this->addItemToInventory($item);
            }
        }
    }

    function addItemToInventory($item, $fillBackpack = false) {
        $events = '';
        if($item == "backpack" && $fillBackpack){
            $events .= $this->fillBackpack();
        }
        if ($item == "day's worth of rations") {
            $this->daysOfFood++;
        }
        if ($item == "canteen" || $item == "some water") {
            $this->daysOfWater++;
        }
        if ($item == "bow and quiver") {
            $this->arrows += 20;
        }
        if ($item == "poison") {
            for ($i = 0; $i < 3; $i++) {
                array_push($this->inventory, "dose of poison");
            }
        }
        while (in_array("poison", $this->inventory)) {
            $this->inventory = removeFromArray("poison", $this->inventory);
        }

        $this->calculateModifiedStrength();
        return $events;
    }

    function fillBackpack() {
        $possibleItems = array("a knife", "a canteen", "fishing gear", "an explosive", "poison");
        $contents = [];
        for ($i = 0; $i < round(rand(0, 5)); $i++) {
            array_push($contents, $possibleItems[round(rand(0, count($possibleItems) - 1))]);
        }
        foreach ($contents as $value) {
            array_push($this->inventory, $value);
            $this->addItemToInventory($value, $this);
        }
        if (count($contents) == 0) {
            return "It contained nothing.<br><br>";
        } else {
            return "It contained " . series($contents) . ".<br><br>";
        }
        $this->calculateModifiedStrength();
    }

    function calculateModifiedStrength() {
        $modStr = 0;

        if (in_array("axe", $this->inventory) || in_array("mace", $this->inventory)) {
            $modStr = $this->strength + 5;
            $this->equippedItem = "an axe";
        } else if ($this->strength < 2.4 && in_array("a knife", $this->inventory) || in_array("knife", $this->inventory)) {
            $knives = 0;
            foreach ($this->inventory as $value) {
                if (strpos("knife", $value) !== false) {
                    $knives++;
                }
            }
            if ($knives > 1) {
                $modStr = 4.8;
                $this->equippedItem = "two knives";
            } else {
                $modStr = 2.4;
                $this->equippedItem = "a knife";
            }
        } else {
            $modStr = $this->strength / 5;
            $this->equippedItem = "";
        }
        $this->modifiedStrength = $modStr;
    }

    function heal() {
        $event = '';
        $event .= $this->nick . " tends to " . (($this->gender == "m") ? "his" : "her") . " injuries.<br><br>";
        if ($this->strength + 1 <= $this->maxStrength) {
            $this->strength += 1;
        } else {
            $this->strength = $this->maxStrength;
        }
        if ($this->health + 1 <= $this->maxHealth) {
            $this->health += 1;
        } else {
            $this->health = $this->maxHealth;
        }
        $this->inventory = removeFromArray("a first aid kit", $this->inventory);
        return $event;
    }

    function lookForWater() {
        $event = $this->nick . " goes searching for water.<br><br>" . (($this->gender == "m") ? "He" : "She");
        if ((0.05 * $this->intelligence) + 0.6 > f_rand()) {
            $this->daysOfWater++;
            $this->daysWithoutWater = 0;
            $event .= " finds a water source and drinks from it.<br><br>";
            if (in_array("empty canteen", $this->inventory)) {
                $canteens = array_count_values($this->inventory)["empty canteen"];
                $event .= (($this->gender == "m") ? "He" : "She") . " fills " . (($this->gender == "m") ? "his" : "her") . " canteen" . (($canteens == 1) ? "" : "s") . ".<br><br>";
                $this->daysOfWater += $canteens;
                for ($i = 0; $i < count($this->inventory); $i++) {
                    if ($this->inventory[$i] == "empty canteen") {
                        $this->inventory[$i] = "canteen";
                    }
                }
            }
            if (in_array("fishing gear", $this->inventory)) {
                $catchResults = floor((($this->dexterity * $this->intelligence) / 2) * f_rand(0.15, 0.45));
                $event .= (($this->gender == "m") ? "He" : "She") . " also fishes and gains " . (($catchResults > 0) ? $catchResults . (($catchResults == 1) ? " day's" : " days'") . " worth of food.<br><br>" : "nothing.<br><br>");
            }
        } else {
            $event .= " doesn't find any.<br><br>";
        }
        return $event;
    }

    function lookForFood() {
        $event = $this->nick . " goes searching for food.<br><br>" . (($this->gender == "m") ? "He" : "She");
        $shootChance = f_rand();
        if (in_array("bow and quiver", $this->inventory) && $this->arrows > 0) {
            $event .= " attempts to shoot a wild animal.<br><br>" . (($this->gender == "m") ? "He" : "She");
            if (0.12 * $this->dexterity > $shootChance) {
                $foodGain = rand(2, 5);
                $event .= " is successful. " . (($this->gender == "m") ? "He" : "She") . " gains " . $foodGain . " days' worth of food.<br><br>";
                $this->daysOfFood += $foodGain;
                for ($i = 0; $i < $foodGain; $i++) {
                    array_push($this->inventory, "a day's worth of rations");
                }
                $this->daysWithoutFood = 0;
            } else {
                $event .= " misses.<br><br>";
            }
        } else {
            if ((0.05 * $this->intelligence) + 0.5 > f_rand()) {
                $this->daysOfFood++;
                array_push($this->inventory, "a day's worth of rations");
                $this->daysWithoutFood = 0;
                $event .= " finds some wild fruit and gains a day's worth of food.<br><br>";
            } else {
                $event .= " doesn't find any.<br><br>";
            }
        }
        return $event;
    }

    function plantExplosive() {
        $this->explosivesPlanted++;
        $this->inventory = removeFromArray("an explosive", $this->inventory);
        return $this->nick . " plants an explosive.<br><br>";
    }

    function attackPlayer(Character $target) {
        $events = [];
        $event = '';
        $arrowDamage = round(f_rand(0.75, 1.75), 2);
        if (in_array("bow and quiver", $this->inventory) && $this->arrows > 0 && $this->strength <= 2.4) {
            $event .= $this->nick . " attempts to shoot " . $target->nick . " with an arrow.<br><br>";
            $this->arrows--;
            if ($this->dexterity * 0.12 > f_rand()) {
                $event .= "A direct hit!<br><br>";
                $target->strength -= $arrowDamage;
                $target->health -= $arrowDamage;
            } else {
                $event .= "However, the arrow misses.<br><br>";
            }
        } else {
            $event .= $this->nick . " attempts to attack " . $target->nick . (($this->equippedItem != "") ? " with " . $this->equippedItem : "") . ".<br><br>";
            if (0.04 * $this->dexterity + 0.7 < f_rand() || 0.04 * $target->dexterity + 0.3 > f_rand()) {
                $event .= "However, it does not connect.<br><br>";
                if (0.3 * ($target->disposition - 2) > f_rand()) {
                    $event .= $target->nick . " prepares to retaliate!<br><br>";
                    if (0.04 * $target->dexterity + 0.75 < f_rand() || 0.04 * $this->dexterity + 0.25 > f_rand()) {
                        $event .= "Unfortunately, this fails as well.<br><br>";
                    } else {
                        $event .= (($target->gender == "m") ? "He" : "She") . " is successful in doing so.<br><br>";
                        $this->strength -= $target->modifiedStrength - $this->defense;
                        $this->health -= $target->modifiedStrength - $this->defense;
                        $this->calculateModifiedStrength();
                        if ($this->health < 0) {
                            array_push($events, $this->nick . " succumbs to " . (($this->gender == "m") ? "his" : "her") . " injuries and dies.<br><br>");
                            $this->die();
                            $target->kill($this);
                        }
                    }
                }
            } else {
                $event .= (($this->gender == "m") ? "He" : "She") . " makes a successful attack.<br><br>";
                $target->strength -= $this->modifiedStrength - $target->defense;
                $target->health -= $this->modifiedStrength - $target->defense;
                $target->calculateModifiedStrength();
            }
        }
        array_push($events, $event);
        if ($target->health < 0) {
            array_push($events, $target->nick . " succumbs to " . (($target->gender == "m") ? "his" : "her") . " injuries and dies.<br><br>");
            $target->die($this->nick);
            $this->kills++;
            $target->killedBy = $this->nick;
            $this->inventory = array_merge($this->inventory, $target->inventory);
            $this->arrows += $target->arrows;
            foreach ($target->inventory as $item) {
                if (!($item == "bow and quiver")) {
                    $event .= $this->addItemToInventory($item);
                }
            }
        }
        return $events;
    }

    function triggerExplosive($targets) {
        $this->explosivesPlanted--;
        foreach ($targets as $target) {
            $target->die($this->nick . "'s explosive", false);
            $this->kill($target);
        }
        $_SESSION['placeToday'] -= count($targets);
        return $this->nick . " sets off an explosive, killing " . nameList($targets) . ".<br><br>";
    }

    function triggerTrap() {
        $event = [];
        array_push($event, $this->nick . " steps on a bear trap.<br><br>");
        $this->strength -= 3;
        $this->health -= 3;
        if ($this->health < 0) {
            $this->die("a bear trap");
            array_push($event, $this->nick . " succumbs to " . (($this->gender == "m") ? "his" : "her") . " injuries and dies.<br><br>");
        }
        return $event;
    }

    function goToSleep() {
        $this->status = "Asleep";
        return $this->nick . " decides to go to sleep.<br><br>";
    }

}
