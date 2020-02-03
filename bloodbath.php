<?php
session_start();
?> 
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="bloodbath.css?v=<?= filemtime("bloodbath.css")?>">
<script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
  <title>Hunger Games Simulator</title>
<?php
$deadToday = [];
$castObject = json_decode($_SESSION['castObject']);
shuffle($castObject);
$castSize = count($castObject);
$place = (int)$_COOKIE['place'];
//echo $place;
function f_rand($min=0,$max=1,$mul=1000000){
    if ($min>$max) return false;
    return mt_rand($min*$mul,$max*$mul)/$mul;
}
function print_r2($val){ //Prints an object to the page in a readable format.
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}
function removeFromArray($value, $array){
   $newArray = $array;
   unset($newArray[array_search($value, $newArray)]);
   $newArray = array_values($newArray);
   return $newArray;
}
function avg_strength($array){
    $strengths = [];
    foreach ($array as $value){
        array_push($strengths, $value->modifiedStrength);
    }
    return array_sum($strengths)/count($strengths);
}
      function compareItems($items){ //This function loops through all the items and evaluates who gets what.
          global $castObject;
          $events = [];
          for($i = 0; $i < count($items); $i++){ //Go through all items available.
              $fightArray = [];
              $otherFighters = [];
              foreach($GLOBALS['castObject'] as $character){
                  if(in_array($i, $character->desiredItems)){
                      array_push($fightArray,$character);
                  }
              }
              if (count($fightArray) == 0){ //If no one wants this item, move on.
                  continue;
              } else if (count($fightArray) == 1){ //If only one person wants this item, give it to them.
                  unset($fightArray[0]->desiredItems[array_search($i, $fightArray[0]->desiredItems)]);
                  array_push($fightArray[0]->inventory,$items[$i]);
                  array_push($events, $fightArray[0]->nick . " grabs " . ((in_array(substr($items[$i], 0,1), ["a", "e", "i", "o"])) ? "an " : "a ") . $items[$i] . ".<br><br>" . addItemToInventory($items[$i], $fightArray[0]));
            $fightArray[0]->modifiedStrength = calculateModifiedStrength($fightArray[0]);
              } else {
                    $characterAttackLottery = [];
                    foreach($fightArray as $fighter){
                        for($j = 0; $j < ($fighter->modifiedStrength * 100 + $fighter->intelligence * 100); $j++){
                            array_push($characterAttackLottery, $fighter);
                        }
                    }
                    shuffle($characterAttackLottery);
                    $strongestCharacter = $characterAttackLottery[rand(0, count($characterAttackLottery) - 1)];
                    $otherFighters = removeFromArray($strongestCharacter, $fightArray);
                    //print_r2($otherFighters);
                    foreach($fightArray as $fighter){
                            $fighter->strength -= (avg_strength(removeFromArray($fighter, $fightArray)) - $fighter->defense) * ((count($otherFighters) == 1) ? 1 : 0.75);
                            $fighter->health -= (avg_strength(removeFromArray($fighter, $fightArray)) - $fighter->defense) * ((count($otherFighters) == 1) ? 1 : 0.75);
                            $fighter->modifiedStrength = calculateModifiedStrength($fighter);
                    }
                    unset($strongestCharacter->desiredItems[array_search($i, $strongestCharacter->desiredItems)]);
                    $strongestCharacter->desiredItems = array_values($strongestCharacter->desiredItems);
                    array_push($strongestCharacter->inventory,$items[$i]);
                    array_push($events, $strongestCharacter->nick . " attacks ". nameList($otherFighters) . (($strongestCharacter->equippedItem != "") ? " with " . $strongestCharacter->equippedItem : "") . " and steals the " . $items[$i] . " that they were " . (count($fightArray) == 2 ? "both" : "all") ." fighting over.<br><br>" . addItemToInventory($items[$i], $strongestCharacter));
              }
              $deadNow = 0;
              foreach($fightArray as $fighter){
                  if($fighter->health < 0){
                      array_push($events, $fighter->nick . " succumbs to " . (($fighter->gender == "m") ? "his" : "her") . " injuries and dies.<br><br>");
                      $fighter->status = "Dead";
                      $deadNow++;
                      $fighter->place = $GLOBALS['place'];
                      $fighter->killedBy = nameList(removeFromArray($fighter, $fightArray));
                      array_push($GLOBALS['deadToday'], $fighter->nick);
                      $fighter->desiredItems = [];
                      array_merge($strongestCharacter->inventory, $fighter->inventory);
                      foreach($fighter->inventory as $item){
                          if($item != "backpack"){
                              addItemToInventory($item, $strongestCharacter);
                          }
                      }
                      foreach(removeFromArray($fighter, $fightArray) as $victor){
                          $victor->kills++;
                      }
                  }
              }
              $GLOBALS['place'] -= $deadNow;
          }
          //setcookie("deadToday", json_encode($GLOBALS['deadToday']), 0, "/");
          return $events;
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
      function series($array){
          if(count($array) == 1){
              return $array[0];
          } else if (count($array) == 2){
              return $array[0] . " and " . $array[1];
          } else {
              $listString = '';
              for($i = 0; $i < count($array) - 1; $i++){
                  $listString .= $array[$i] . ", ";
              }
              $listString .= "and " . end($array);
              return $listString;
          }
      }
      function initializeItems(){
          global $castSize;
          $items = [];
          for($i = 0; $i < 2 * $castSize; $i++){
              array_push($items,"day's worth of rations");
              array_push($items,"canteen");
          }
          for($i = 0; $i < round(0.9 * $castSize); $i++){
              array_push($items,"knife");
              array_push($items,"bow and quiver");
          }
          for($i = 0; $i < round(0.35 * $castSize); $i++){
              array_push($items,"backpack");
          }
          for($i = 0; $i < round(0.15 * $castSize); $i++){
              array_push($items,"mace");
              array_push($items,"axe");
          }
          shuffle($items);
          return $items;
      }
      function showEvents($events){
          global $castObject;
          echo "<div class='text-center'>";
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
          echo "</div>";
      }
    function getCharacterByEvent($event){
        global $castObject;
        $characterArray = [];
        foreach($castObject as $character){
            if(strpos($event, $character->nick . " ") !== FALSE || strpos($event, $character->nick . ".") !== FALSE || strpos($event, $character->nick . ",") !== FALSE){
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
      function addItemToInventory($item, $character){
          $events = '';
          if($item == "backpack"){
              $events .= fillBackpack($character);
          }
          if($item == "day's worth of rations"){
              $character->daysOfFood++;
          }
          if($item == "canteen"){
              $character->daysOfWater++;
          }
          if($item == "bow and quiver"){
              $character->arrows += 20;
          }
          $character->modifiedStrength = calculateModifiedStrength($character);
          return $events;
      }
      function fillBackpack($character){
          $possibleItems = array("a knife", "a canteen", "fishing gear", "an explosive");
          $contents = [];
          for($i = 0; $i < round(rand(0, 5));$i++){
              array_push($contents, $possibleItems[round(rand(0, count($possibleItems)-1))]);
          }
          foreach($contents as $value){
              array_push($character->inventory, $value);
              addItemToInventory($value, $character);
          }
          if(count($contents) == 0){
              return "It contained nothing.<br><br>";
          } else{
              return "It contained " . series($contents) . ".<br><br>";
          }
          calculateModifiedStrength($character);
      }
    function calculateModifiedStrength($character){
    $modStr = 0;

    if(in_array("axe", $character->inventory) || in_array("mace", $character->inventory)){
        $modStr = $character->strength + 5;
        $character->equippedItem = "an axe";
    } else if($character->strength < 2.4 && in_array("a knife", $character->inventory) || in_array("knife", $character->inventory)){
        $knives = 0;
        foreach ($character->inventory as $value) {
            if(strpos("knife", $value) !== false){
                $knives++;
            }
        }
        if($knives > 1){
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
        $items = initializeItems();
        $events = [];  
        foreach($castObject as $character){
            if($character->intelligence <= 3 && 1 - ($character->intelligence * 0.025) < f_rand()){
                array_push($events, $character->nick . " steps off " . (($fighter->gender == "m") ? "his" : "her") . " podium too early and explodes.<br><br>");
                $character->status = "Dead";
                $character->killedBy = (($fighter->gender == "m") ? "his" : "her") . " podium";
                $character->place = $GLOBALS['place']--;
                array_push($GLOBALS['deadToday'], $character->nick);
                $character->desiredItems = [];
                
            }
            $runawayChance = [0.8, 0.65, 0.35, 0.15, 0.05];
            if(1 - $runawayChance[$character->disposition - 1] < f_rand() && $character->status == "Alive"){
                array_push($events, $character->nick . " runs away from the Cornucopia.<br><br>");
            } else {
                for($i = 0; $i < round(f_rand(1.5, 1.75) * $character->disposition); $i++){
                    if($character->status == "Alive"){
                        array_push($character->desiredItems,round(rand(0,count($items)-1)));
                    }
                }
            }
        }
        $events += compareItems($items);
        ?>
<div class="text-center" style="height:100%">
    <h1>Bloodbath</h1>
          <?php
          //print_r2($castObject);
          showEvents($events);
          ?>
              <button class="btn btn-primary" onclick="next()">Continue</button>
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
        console.log(JSON.stringify(<?= json_encode($castObject)?>));
        $.ajax({
              url: "editFile.php",
              async: false,
              method: "POST",
              data: JSON.stringify(<?= json_encode($castObject)?>),
              contentType: "text/plain",
              dataType: "text",
              success: function(response){
                  console.log(response);
              },
              error: function(jqXHR, textStatus, errorThrown){
                  console.log(textStatus);
                  console.log(errorThrown);
              }
          });
          document.cookie = "deadToday=" + '<?php echo json_encode($GLOBALS['deadToday'])?>';
          document.cookie = "place=" + <?php echo $GLOBALS['place']?>;
          window.location = "day.php";
    }
</script>
