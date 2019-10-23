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
              if($character->strength > $strongestCharacter->strength){
                  $strongestCharacter = $character;
              }
          }
          return $strongestCharacter;
      }
      function compareItems($castObject, $items){ //This function loops through all the items and evaluates who gets what.
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
              } else {
                  unset($fightArray[0]->desiredItems[array_search($i, $fightArray[0]->desiredItems)]);
                  array_push($fightArray[0]->inventory,$items[$i]);
                  array_push($events, $fightArray[0]->nick . " got " . $items[$i] . ".<br><br>");
              }
              unset($fightArray);
          }
          return $events;
      }
      function initializeItems(){
          global $castSize;
          $items = [];
          for($i = 0; $i < 2 * $castSize; $i++){
              array_push($items,"some food");
              array_push($items,"some water");
          }
          for($i = 0; $i < round($castSize); $i++){
              array_push($items,"a knife");
              array_push($items,"a bow");
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
              ?>
<img src="<?= getCharacterByEvent($event)->image?>" width="90" height="90">
<br>
              <?php
              echo $event;
          }
          echo "</div>";
      }
      function getCharacterByEvent($event){
          global $castObject;
          foreach($castObject as $character){
              if(substr($event, 0, strlen($character->nick)) == $character->nick){
                  return $character;
              }
          }
      }
        $items = initializeItems();
          foreach($castObject as $character){
              for($i = 0; $i < 7; $i++){
                  array_push($character->desiredItems,round(rand(0,count($items)-1)));
              }
          }
          $events = compareItems($castObject, $items);
          shuffle($events);
          ?>
<div class="text-center">
    <h1>Bloodbath</h1>
</div>
          <?php
          showEvents($events);

