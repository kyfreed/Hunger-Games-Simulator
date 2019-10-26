<link rel="stylesheet" type="text/css" href="index.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<?php
$castObject = json_decode($_COOKIE['castObject']);
$castSize = count($castObject);
function print_r2($val){ //Prints an object to the page in a readable format.
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}
function hasDuplicateHighs($array){ //Checks if an array has two or more high values.
        $dupe_array = array();
        foreach ($array as $val) {
            if (++$dupe_array[$val] > 1 && max($array) == $val) {
                return true;
            }
        }
        return false;
      }
      function strongestCharacter($characters){ //In an array of characters, return the strongest one.
          $strongestCharacter = $characters[0];
          foreach($characters as $character){
              if($character->modifiedStrength > $strongestCharacter->modifiedStrength){
                  $strongestCharacter = $character;
              }
          }
          return $strongestCharacter;
      }
      function compareItems($items){ //This function loops through all the items and evaluates who gets what.
          global $castObject;
          $events = [];
          for($i = 0; $i < count($items); $i++){ //Go through all items available.
              $fightArray = [];
              foreach($castObject as $character){
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
                  $strengthsArray = [];
                  foreach($fightArray as $fighter){
                      array_push($strengthsArray, $fighter->modifiedStrength);
                  }
                  if(!hasDuplicateHighs($strengthsArray)){
                    $strongestCharacter = strongestCharacter($fightArray);
                  } else {
                    $strongestCharacters = [];
                    foreach($fightArray as $fighter){
                        if($fighter->modifiedStrength == max($strengthsArray)){
                            array_push($strongestCharacters, $fighter);
                        }
                    }
                    $strongestCharacter = ($strongestCharacters[floor(rand(0, count($strongestCharacters)))]);
                  }
                    $otherFighters = $fightArray;
                    unset($otherFighters[array_search($strongestCharacter, $otherFighters)]);
                    $otherFighters = array_values($otherFighters);
                    foreach($fightArray as $fighter){
                        if($fighter->modifiedStrength == max($strengthsArray)){
                            $fighter->strength -= (count($fightArray) / 2);
                            $fighter->modifiedStrength = calculateModifiedStrength($fighter);
                        } else {
                            $fighter->strength -= $strongestCharacter->modifiedStrength - $fighter->modifiedStrength;
                            //echo $fighter->nick . "'s strength is now " . $fighter->strength . ".<br><br>";
                            $fighter->modifiedStrength = calculateModifiedStrength($fighter);
                        }
                    }
                    unset($strongestCharacter->desiredItems[array_search($i, $strongestCharacter->desiredItems)]);
                    $strongestCharacter->desiredItems = array_values($strongestCharacter->desiredItems);
                    array_push($strongestCharacter->inventory,$items[$i]);
                    array_push($events, $strongestCharacter->nick . " attacks ". nameList($otherFighters) ." and steals the " . $items[$i] . " that they were " . (count($fightArray) == 2 ? "both" : "all") ." fighting over.<br><br>" . addItemToInventory($items[$i], $strongestCharacter));
              }
              foreach($otherFighters as $fighter){
                  if($fighter->strength < 0){
                      array_push($events, $fighter->nick . " succumbs to " . (($fighter->gender == "m") ? "his" : "her") . " injuries and dies.<br><br>");
                      $fighter->status = "Dead";
                      unset($fighter->desiredItems);
                  }
              }
              unset($fightArray);
              unset($otherFighters);
          }
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
          for($i = 0; $i < 1.5 * $castSize; $i++){
              array_push($items,"day's worth of rations");
              array_push($items,"canteen");
          }
          for($i = 0; $i < round($castSize); $i++){
              array_push($items,"knife");
              array_push($items,"bow and quiver");
          }
          for($i = 0; $i < round(0.5 * $castSize); $i++){
              array_push($items,"backpack");
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
              if(strpos($event, $character->nick) !== false){
                  if(count($characterArray) == 0 || firstAfter($character->nick, $characterArray, $event) == -1){
                      array_push($characterArray, $character);
                  } else {
                      array_splice($characterArray, 0, 0, array($character));
                      
                  }
              }
          }
          return $characterArray;
      }
      function firstAfter($sub, $array,$string){
          global $castObject;
          for($i = 0; $i < count($array); $i++) {
              if(strpos($string, $sub) < strpos($string, $array[$i]->nick)){
                  return $i;
              }
          }
          return -1;
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
          $character->modifiedStrength = calculateModifiedStrength($character);
          return $events;
      }
      function fillBackpack($character){
          $possibleItems = array("a knife", "a canteen", "fishing gear", "poison");
          $contents = [];
          for($i = 0; $i < round(rand(0, 5));$i++){
              array_push($contents, $possibleItems[round(rand(0, count($possibleItems)-1))]);
          }
          array_push($character->inventory, $contents);
          if(count($contents) == 0){
              return "It contained nothing.<br><br>";
          } else{
              return "It contained " . series($contents) . ".<br><br>";
          }
          
      }
      function calculateModifiedStrength($character){
          $arrowDamage = round(rand(0.75, 1.75), 2);
          if($character->strength < $arrowDamage && in_array("bow and quiver", $character->inventory)){
              return $arrowDamage;
          }
          if($character->strength < 2.4 && in_array("a knife", $character->inventory) || in_array("knife", $character->inventory)){
              $knives = 0;
              foreach ($character->inventory as $value) {
                  if(strpos("knife", $value) !== false){
                      $knives++;
                  }
              }
              if(knives > 1){
                  return 4.8;
              }
              return 2.4;
          }
          if(in_array("axe", $character->inventory) || in_array("mace", $character->inventory)){
              return $character->strength + 5;
          }
          return $character->strength / 5;
      }
        $items = initializeItems();
          foreach($castObject as $character){
              for($i = 0; $i < round(rand(1.5, 1.75) * $character->disposition); $i++){
                  array_push($character->desiredItems,round(rand(0,count($items)-1)));
              }
          }
          $events = compareItems($items);
          ?>
<div class="text-center">
    <h1>Bloodbath</h1>
</div>
          <?php
          showEvents($events);
          ?>
          <div class="text-center">
              <button class="btn btn-primary" onclick="next()">Continue</button>
          </div>
<script>
    function next(){
        document.cookie = "castObject=" + JSON.stringify(<?= json_encode($castObject)?>);
        window.location = 'day.php';
    }
</script>
