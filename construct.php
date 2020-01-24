<?php
session_start();
          function randomDigits(){
              do{
                clearstatcache();
                $digits = "";
                for($i = 0; $i < 12; $i++){
                  $digits .= round(rand(0,9));
                }
              } while(file_exists("castObject" . $digits . ".txt") === TRUE);
              return $digits;
          }
          $castSize = $_POST['castSize'];
          $castObject = [];
          for($i = 0; $i < $castSize; $i++){
            $r = strval(rand(0, 255));
            $g = strval(rand(0, 255));
            $b = strval(rand(0, 255));
            $tempObject->name = htmlspecialchars($_POST["castName" . $i]);
            $tempObject->nick = ((in_array("castNick" . $i, $_POST)) ? htmlspecialchars($_POST["castNick" . $i]) : $tempObject->name);
            $tempObject->gender = $_POST["castGender" . $i];
            $tempObject->disposition = $_POST["castDisposition" . $i];
            $tempObject->strength = ((in_array("castStrength" . $i, $_POST)) ? (int) $_POST["castStrength" . $i] : 5);

            $tempObject->health = ((in_array("castHealth" . $i, $_POST)) ? (int) $_POST["castHealth" . $i] : 5);
            $tempObject->maxStrength = $tempObject->strength;
            $tempObject->modifiedStrength = $tempObject->strength / 5;
            $tempObject->dexterity = ((in_array("castDex" . $i, $_POST)) ? (int) $_POST["castDex" . $i] : 5);
            $tempObject->intelligence = ((in_array("castInt" . $i, $_POST)) ? (int) $_POST["castInt" . $i] : 5);
            $tempObject->charisma = ((in_array("castCha" . $i, $_POST)) ? (int) $_POST["castCha" . $i] : 5);
            $tempObject->defense = 0;
            $tempObject->image = ((in_array("castImage" . $i, $_POST)) ? $_POST["castImage" . $i] : "generateImage.php?imageInitial=" . substr(htmlspecialchars($_POST["castName" . $i]),0,1) . "&r=" . $r . "&g=" . $g . "&b=" . $b);
            $tempObject->status = "Alive";
            $tempObject->actionTaken = "false";
            $tempObject->daysOfFood = 1;
            $tempObject->daysWithoutFood = 0;
            $tempObject->daysOfWater = 1;
            $tempObject->desiredItems = [];
            $tempObject->inventory = [];
            $tempObject->arrows = 0;
            $tempObject->explosivesPlanted = 0;
            $tempObject->memberOfAlliance = -1;
            $tempObject->equippedItem = "";
            $tempObject->kills = 0;
            $tempObject->daysAlive = 0;
            $tempObject->orderMarker = $i;
            $tempObject->place = 0;
            array_push($castObject,clone $tempObject);
          }
          $_SESSION['castObject'] = $castObject;
echo $filename;