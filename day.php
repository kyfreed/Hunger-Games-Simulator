<link rel="stylesheet" type="text/css" href="day.css?v=1.6">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
        <script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
  <title>Hunger Games Simulator</title>
  <div class="text-center" style="height: 100%">
      <h1>Day <?=$_COOKIE['counter']?></h1>
<?php
$castObject = json_decode(file_get_contents($_COOKIE['castObjectFile']));
          shuffle($castObject);
          //print_r2($castObject);
          $castSize = count($castObject);
$deadToday = json_decode($_COOKIE['deadToday']);
function f_rand($min=0,$max=1,$mul=1000000){
    if ($min>$max) return false;
    return mt_rand($min*$mul,$max*$mul)/$mul;
}
function removeFromArray($value, $array){
   if(!in_array($value, $array)){
       return $array;
   }
   $newArray = $array;
   unset($newArray[array_search($value, $newArray)]);
   $newArray = array_values($newArray);
   return $newArray;
}
function print_r2($val){ //Prints an object to the page in a readable format.
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}
function beginningOfDay($character){
    $event = '';
        if($character->daysOfFood == 0){
            $character->daysWithoutFood++;
        } else {
            $character->daysOfFood--;
            $character->inventory = removeFromArray("day's worth of rations", $character->inventory);
        }
        if($character->daysWithoutFood > 1){
            $character->strength--;
            if($character->strength < 0){
                $event .= $character->nick . " starves to death.<br><br>";
                $character->status = "Dead";
                array_push($GLOBALS['deadToday'], $character->nick);
            }
        }
        if($character->daysOfWater == 0){
            $character->strength -= 1.5;
            if($character->strength < 0){
                $event .= $character->nick . " dies of thirst.<br><br>";
                $character->status = "Dead";
                array_push($GLOBALS['deadToday'], $character->nick);
            }
        } else {
        $character->daysOfWater++;
        }
    return $event;
}
function sponsor($character){
    $event = '';
    $sponsorItems = ["an explosive", "some water", "a first aid kit"];
    if(0.0625 * $character->charisma > f_rand()){
        $randItem = $sponsorItems[rand(0, count($sponsorItems) - 1)];
        $event .= $character->nick . " receives " . $randItem . " from an unknown sponsor.<br><br>" . addItemToInventory($randItem, $character);
        array_push($character->inventory, $randItem);
    }
    return $event;
}
function calculateModifiedStrength($character){
    $modStr = 0;

    if(in_array("axe", $character->inventory) || in_array("mace", $character->inventory)){
        $modStr = $character->strength + 5;
    } else if($character->strength < 2.4 && in_array("a knife", $character->inventory) || in_array("knife", $character->inventory)){
        $knives = 0;
        foreach ($character->inventory as $value) {
            if(strpos("knife", $value) !== false){
                $knives++;
            }
        }
        if($knives > 1){
            $modStr = 4.8;
        } else {
            $modStr = 2.4;
        } 
    } else {
        $modStr = $character->strength / 5;
    }
    return $modStr;
}
function showEvents($events){
          global $castObject;
          foreach($events as $event){
              foreach(getCharacterByEvent($event) as $character){
              ?>
<img src="<?= $character->image?>" width="90" height="90"/>
              <?php
              }
              ?>
              <br>
              <?php
              echo $event;
          }
}
function getCharacterByEvent($event){
        global $castObject;
        $characterArray = [];
        foreach($castObject as $character){
            if(strpos($event, $character->nick) !== FALSE){
                if(count($characterArray) == 0 || firstAfter($character->nick, $characterArray, $event) == -1){
                    array_push($characterArray, $character);
                } else {
                    array_splice($characterArray, firstAfter($character->nick, $characterArray, $event), 0, array($character));

                }
            }
        }
        return $characterArray;
    }
    function firstAfter($sub, $array, $string){
        global $castObject;
        $index = -1;
        $strPosAfter = strlen($string);
        for($i = 0; $i < count($array); $i++) {
            if(strpos($string, $sub) < strpos($string, $array[$i]->nick) && strpos($string, $array[$i]->nick) < $strPosAfter){
                $index = $i;
                $strPosAfter = strpos($string, $array[$i]->nick);
            }
        }
        return $index;
    }
function lookForWater($character){
    $event = $character->nick . " goes searching for water.<br><br>" . (($character->gender == "m") ? "He" : "She");
    if((0.05 * $character->intelligence) + 0.4 > f_rand()){
        $character->daysOfWater++;
        $event .= " finds a water source and drinks from it.<br><br>";
        if(in_array("canteen", $character->inventory)){
            $canteens = array_count_values($character->inventory)["canteen"];
            $event .= (($character->gender == "m") ? "He" : "She") . " also fills " . (($character->gender == "m") ? "his" : "her") . " canteen" . (($canteens == 1) ? "" : "s") . ".<br><br>";
            $character->daysOfWater += $canteens;
        }
    } else {
        $event .= " doesn't find any.<br><br>";
    }
    return $event;
}
function lookForFood($character){
    $event = $character->nick . " goes searching for food.<br><br>" . (($character->gender == "m") ? "He" : "She");
    $shootChance = f_rand();
    if(in_array("bow and quiver", $character->inventory) && $character->arrows > 0){
        $event .= " attempts to shoot a wild animal.<br><br>" . (($character->gender == "m") ? "He" : "She");
        if(0.12 * $character->dexterity > $shootChance){
            $foodGain = rand(2, 5);
            $event .= " is successful. " . (($character->gender == "m") ? "He" : "She") . " gains " . $foodGain . " days' worth of food.<br><br>";
            $character->daysOfFood += $foodGain;
            $character->daysWithoutFood = 0;
            
        } else {
            $event .= " misses.<br><br>";
        }
    } else {
        if((0.05 * $character->intelligence) + 0.4 > f_rand()){
        $character->daysOfFood++;
        $character->daysWithoutFood = 0;
        $event .= " finds some wild fruit and gains a day's worth of food.<br><br>";
        } else {
            $event .= " doesn't find any.<br><br>";
        }
    }
    return $event;
}
function attackPlayer($character, $target){
    $event = '';
    $event .= $character->nick . " attempts to attack " . $target->nick . ".<br><br>";
    if(in_array("bow and quiver", $character->inventory) && $character->arrows > 0 && $character->strength <= 2.4){
        $event .= $character->nick . " lets loose an arrow!<br><br>";
        $character->arrows--;
        if($character->dexterity * 0.12 > f_rand()){
            $event .= "A direct hit!<br><br>";
            $target->strength -= round(f_rand(0.75,1.75),2);
        } else {
            $event .= "However, the arrow misses.<br><br>";
        }
    } else {
        if(0.04 * $character->dexterity + 0.7 < f_rand() || 0.04 * $target->dexterity + 0.3 > f_rand()){
            $event .= "However, it does not connect.<br><br>";
            if(0.3 * ($target->disposition-2) > f_rand()){
                $event .= $target->nick . " prepares to retaliate!<br><br>";
                if(0.04 * $target->dexterity + 0.75 < f_rand() || 0.04 * $character->dexterity + 0.25 > f_rand()){
                    $event .= "Unfortunately, this fails as well.<br><br>";
                } else {
                    $event .= (($target->gender == "m") ? "He" : "She") . " is successful in doing so.<br><br>";
                    $character->strength -= $target->modifiedStrength - $character->defense;
                }
            }
        } else {
            $event .= (($character->gender == "m") ? "He" : "She") . " makes a successful attack.<br><br>";
            $target->strength -= $character->modifiedStrength - $target->defense;
        }
    }
//    if($character->strength < 0){
//        array_push($events, $character->nick . " succumbs to " . (($character->gender == "m") ? "his" : "her") . " injuries and dies.<br><br>");
//        $character->status = "Dead";
//        array_push($GLOBALS['deadToday'], $character->nick);
//    }
//    if($target->strength < 0){
//        array_push($events, $target->nick . " succumbs to " . (($target->gender == "m") ? "his" : "her") . " injuries and dies.<br><br>");
//        $target->status = "Dead";
//        array_push($GLOBALS['deadToday'], $target->nick);
//    }
    return $event;
}
function plantExplosive($character){
    $character->explosivesPlanted++;
    $character->inventory = removeFromArray("an explosive", $character->inventory);
    return $character->nick . " plants an explosive.<br><br>";
}
function triggerExplosive($character, $targets){
    $character->explosivesPlanted--;
    foreach ($targets as $target){
        $target->status = "Dead";
        array_push($GLOBALS['deadToday'], $target->nick);
    }
    return $character->nick . " sets off an explosive, killing " . nameList($targets) . ".<br><br>";
}
function triggerTrap($character){
    $event = '';
    $event .= $character->nick . " steps on a bear trap.<br><br>";
    $character->strength -= 3;
    return $event;
}

function heal($character){
    $event = '';
    $event .= $character->nick . " tends to " . (($character->gender == "m") ? "his" : "her") . " injuries.<br><br>";
    if($character->strength + 1 <= $character->maxStrength){
        $character->strength += 1;
    } else {
        $character->strength = $character->maxStrength;
    }
    $character->inventory = removeFromArray("a first aid kit", $character->inventory);
    return $event;
}
function nameList($array){
    if(count($array) == 1){
        return $array[0]->nick;
    } else if (count($array) == 2){
        return $array[0]->nick . " and " . $array[1]->nick;
    } else {
        $listString = '';
        for($i = 0; $i < count($array) - 1; $i++){
            $listString .= $array[$i]->nick . ", ";
        }
        $listString .= "and " . end($array)->nick;
        return $listString;
    }
}
function addItemToInventory($item, $character){
          $events = '';
          if($item == "backpack"){
              $events .= fillBackpack($character);
          }
          if($item == "day's worth of rations"){
              $character->daysOfFood++;
          }
          if($item == "canteen" || $item == "some water"){
              $character->daysOfWater++;
          }
          if($item == "bow and quiver"){
              $character->arrows += 20;
          }
          $character->modifiedStrength = calculateModifiedStrength($character);
          return $events;
}
function action($character){
    global $castObject;
    $event = '';
    $chosenAction = weightedActionChoice($character);
    if($chosenAction == "look for food"){
            $event .= lookForFood($character);
            $character->actionTaken = "true";
    } else if ($chosenAction == "attack another player"){
            do{
                $target = $castObject[round(rand(0, count($castObject)-1))];
                //$target = $castObject[0];
            } while($target == $character || $target->status != "Alive");
            $event .= attackPlayer($character, $target);
            $character->actionTaken = "true";
            $target->actionTaken = "true";
    } else if ($chosenAction == "look for water"){
            $event .= lookForWater($character);
            $character->actionTaken = "true";
    } else if ($chosenAction == "plant explosive"){
            $event .= plantExplosive($character);
            $character->actionTaken = "true";
    } else if (strpos($chosenAction, "explode") === 0){
            //echo "Initiating explosion...";
            $targets = [];
            $remainingTargets = $castObject;
            $i = 0;
            while($i < (int) substr($chosenAction, -1)){
                $target = $remainingTargets[rand(0, count($remainingTargets) - 1)];
                if(!(in_array($target, $targets) || $target->status == "Dead" || $target == $character)){
                    array_push($targets, $target);
                    $remainingTargets = removeFromArray($target, $remainingTargets);
                    $i++;
                }
                
            }
            //print_r2($targets);
            $event .= triggerExplosive($character, $targets);
            $character->actionTaken = "true";
            foreach ($targets as $target){
                $target->actionTaken = "true";
            }
        } else if ($chosenAction == "trigger trap"){
            $event .= triggerTrap($character);
            $character->actionTaken = "true";
        } else if($chosenAction == "heal"){
            $event .= heal($character);
            $character->actionTaken = "true";
        }
    
    return $event;
}
function weightedActionChoice($character){
    $attackChance = [0.05, 0.15, 0.35, 0.65, 0.85];
    if(0.25 > f_rand() && 0.04 * $character->intelligence > f_rand()){
        return "trigger trap";
    } else if($character->daysOfFood < 2){
        return "look for food";
    } else if ($character->daysOfWater < 2){
        return "look for water";
    } else if ($character->strength < 1.5 && in_array("a first aid kit", $character->inventory)){ 
        return "heal";
    } else if (in_array("an explosive", $character->inventory) && $character->disposition >= 3 && 0.3 * ($character->disposition-2) > f_rand()){
        return "plant explosive";
    } else if ($character->explosivesPlanted > 0){
        $numTargets = rand(0, 4);
        $targets = [];
        if($numTargets >= 2){
            //echo "Explosive triggered.";
            return "explode " . $numTargets;
        }
        
    } else if($attackChance[$character->disposition - 1] > f_rand()){
        return "attack another player";
    } else {
        if(($character->daysOfWater/$character->daysOfFood) * 0.5 > f_rand()){
            return "look for food";
        } else {
            return "look for water";
        }
    }
}
$events = [];
 foreach($GLOBALS['castObject'] as $character){
     if($character->status == "Alive"){
         $beginning = beginningOfDay($character);
         if($beginning != ''){
             array_push($events, $beginning);
         }
     }
 }
 foreach($GLOBALS['castObject'] as $character){
     if($character->status == "Alive"){
         $sponsor = sponsor($character);
         if($sponsor != ''){
             array_push($events, $sponsor);
         }
     }
 }
foreach($GLOBALS['castObject'] as $character){
    if($character->actionTaken == "false" && $character->status == "Alive"){
        array_push($events, action($character));
    }
    foreach($GLOBALS['castObject'] as $fighter){
      if($fighter->strength < 0 && $fighter->status == "Alive"){
        array_push($events, $fighter->nick . " succumbs to " . (($fighter->gender == "m") ? "his" : "her") . " injuries and dies.<br><br>");
        $fighter->status = "Dead";
        array_push($GLOBALS['deadToday'], $fighter->nick);
      }
    }
    //setcookie("deadToday", json_encode($GLOBALS['deadToday']), 0, "/");
}
//print_r2($events);
showEvents($events);
$playersAlive = 0;
$nextDestination = 'deadTributes.php';
foreach($castObject as $character){
    $character->actionTaken = "false";
    if($character->status == "Alive"){
        $playersAlive++;
    }
}
if($playersAlive == 1){
    $nextDestination = 'winner.php';
}
?>
    <div class="text-center">
    <button class="btn btn-primary" onclick="next()">Continue</button>
</div>
  </div>
    <script>
    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i <ca.length; i++) {
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
    function next(){
        $.ajax({
              url: "editFile.php",
              async: false,
              method: "POST",
              data: "castObject=" + JSON.stringify(<?= json_encode($castObject)?>) + "&fileName=" + getCookie("castObjectFile"),
              dataType: "text",
              success: function(castCookie){
                  cookie = castCookie;
              },
              error: function(jqXHR, textStatus, errorThrown){
                  console.log(textStatus);
                  console.log(errorThrown);
              }
          });
          document.cookie = "deadToday=" + '<?php echo json_encode($GLOBALS['deadToday'])?>';
          window.location = "<?php echo $nextDestination;?>";
    }
</script>