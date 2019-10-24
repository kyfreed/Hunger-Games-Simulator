<link rel="stylesheet" type="text/css" href="index.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<?php
function print_r2($val){ //Prints an object to the page in a readable format.
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}
$castObject = json_decode($_COOKIE['castObject']);
$castSize = count($castObject);
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
                  array_push($events, $fightArray[0]->nick . " grabs " . $items[$i] . ".<br><br>");
//                  $events .= addItemToInventory($items[$i], $strongestCharacter);
              } else {
                  $strengthsArray = [];
                  foreach($fightArray as $fighter){
                      array_push($fighter->modifiedStrength, $strengthsArray);
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
                            calculateModifiedStrength($fighter);
                        } else {
                            $fighter->strength -= $strongestCharacter->modifiedStrength - $fighter->modifiedStrength;
                            calculateModifiedStrength($fighter);
                        }
                    }
                    unset($strongestCharacter->desiredItems[array_search($i, $strongestCharacter->desiredItems)]);
                    array_push($strongestCharacter->inventory,$items[$i]);
                    array_push($events, $strongestCharacter->nick . " fights ". nameList($otherFighters) ." to get " . $items[$i] . ".<br><br>");
//                    $events .= addItemToInventory($items[$i], $strongestCharacter);
              }
              unset($fightArray);
              foreach($otherFighters as $fighter){
                  if($fighter->strength < 0){
                      array_push($events, $fighter->nick . " succumbs to " . (($fighter->gender == "m") ? "his" : "her") . " injuries and dies.<br><br>");
                  }
              }
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
      function initializeItems(){
          global $castSize;
          $items = [];
          for($i = 0; $i < 1.5 * $castSize; $i++){
              array_push($items,"a day's worth of rations");
              array_push($items,"a canteen");
          }
          for($i = 0; $i < round($castSize); $i++){
              array_push($items,"a knife");
              array_push($items,"a bow and a quiver of arrows");
          }
          for($i = 0; $i < round(0.5 * $castSize); $i++){
              array_push($items,"a backpack");
              array_push($items,"a mace");
              array_push($items,"an axe");
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
          calculateModifiedStrength($character);
          $events = '';
          if($item == "a backpack"){
              $events .= fillBackpack($character);
          }
          return $events;
      }
      function fillBackpack($character){
          $possibleItems = array("a knife", "a canteen", "some fishing gear", "poison");
          $contents = [];
          for($i = 0; $i < round(rand(0, 5));$i++){
              array_push($contents, $possibleItems[round(rand(0, count($possibleItems)-1))]);
          }
          array_push($character->inventory, $contents);
          return "It contained " . nameList($contents) . ".<br><br>";
          
      }
      function calculateModifiedStrength($character){
          if(in_array("a bow and a quiver of arrows", $character->inventory)){
              return 1.5;
          }
          if(in_array("a knife", $character->inventory)){
              return 3;
          }
          if(in_array("an axe", $character->inventory) || in_array("a mace", $character->inventory)){
              return $character->strength + 5;
          }
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

