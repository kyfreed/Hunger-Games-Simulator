        <link rel="stylesheet" type="text/css" href="index.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
        <script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
  <div class="text-center">
      <h1>Day <?=$_COOKIE['counter']?></h1>
  </div>
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
function print_r2($val){ //Prints an object to the page in a readable format.
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}
function calculateModifiedStrength($character){
          $arrowDamage = round(f_rand(0.75, 1.75), 2);
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
function lookForFood($character){
    $event = $character->nick . " goes searching for food.<br><br>" . (($character->gender == "m") ? "He" : "She");
    $shootChance = f_rand();
    if(in_array("bow and quiver", $character->inventory) && $character->arrows > 0){
        $event .= " attempts to shoot a wild animal.<br><br>" . (($character->gender == "m") ? "He" : "She");
        if($shootChance > 0.12 * $character->dexterity){
            $foodGain = rand(2, 5);
            $event .= " is successful. " . (($character->gender == "m") ? "He" : "She") . " gains " . $foodGain . " days' worth of food.<br><br>";
        } else {
            $event .= " misses.<br><br>";
        }
    } else {
        if((0.05 * $character->intelligence) + 0.4 > f_rand()){
        $character->daysOfFood++;
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
    if(in_array("bow and quiver", $character->inventory) && $character->arrows > 0){
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
                if(0.04 * $target->dexterity + 0.7 < f_rand() || 0.04 * $character->dexterity + 0.3 > f_rand()){
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
    setcookie("deadToday", json_encode($GLOBALS['deadToday']), 0, "/");
    return $event;
}
function action($character){
    global $castObject;
    $event = '';
    $possibleActions = getPossibleActions($character);
    $chosenAction = weightedActionChoice($character, $possibleActions);
    switch ($chosenAction){
        case "look for food":
            $event .= lookForFood($character);
            $character->actionTaken = "true";
            break;
        case "attack another player":
            do{
                $target = $castObject[round(rand(0, count($castObject)-1))];
                //$target = $castObject[0];
            } while($target == $character || $target->status != "Alive");
            $event .= attackPlayer($character, $target);
            $character->actionTaken = "true";
            $target->actionTaken = "true";
            break;
    }
    return $event;
}
function getPossibleActions($character){
    $actions = [];
    if($character->actionTaken == "false"){
        array_push($actions, "look for food");
    }
    if($character->actionTaken == "false" && $character->disposition >= 3){
        array_push($actions, "attack another player");
    }
    //print_r2($actions);
    return $actions;
}
function weightedActionChoice($character, $actions){
    if(in_array("attack another player", $actions) && 0.3 * ($character->disposition-2) > f_rand()){
        return "attack another player";
    } else {
        return "look for food";
    }
}
          $events = [];
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
          window.location = "<?php echo $nextDestination;?>";
    }
              </script>