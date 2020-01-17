<?php
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
          $castSize=count($_POST)/9;
          $castObject = [];
          for($i = 1; $i <= $castSize; $i++){
            $tempObject->name = htmlspecialchars($_POST["castName" . $i]);
            $tempObject->nick = (($_POST["castNick" . $i] != "") ? htmlspecialchars($_POST["castNick" . $i]) : $tempObject->name);
            $tempObject->gender = $_POST["castGender" . $i];
            $tempObject->disposition = (($_POST["castDisposition" . $i] != "") ? (int) $_POST["castDisposition" . $i] : 5);
            $tempObject->strength = (($_POST["castStrength" . $i] != "") ? (int) $_POST["castStrength" . $i] : 5);
            $tempObject->health = (($_POST["castHP" . $i] != "") ? (int) $_POST["castHP" . $i] : 5);
            $tempObject->maxStrength = $tempObject->strength;
            $tempObject->modifiedStrength = $tempObject->strength / 5;
            $tempObject->dexterity = (($_POST["castDex" . $i] != "") ? (int) $_POST["castDex" . $i] : 5);
            $tempObject->intelligence = (($_POST["castInt" . $i] != "") ? (int) $_POST["castInt" . $i] : 5);
            $tempObject->charisma = (($_POST["castCha" . $i] != "") ? (int) $_POST["castCha" . $i] : 5);
            $tempObject->defense = 0;
            $tempObject->image = (($_POST["castImage" . $i] != "") ? $_POST["castImage" . $i] : "generateImage.php?imageInitial=" . substr(htmlspecialchars($_POST["castName" . $i]),0,1));
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
            array_push($castObject,clone $tempObject);
          }
          $filename = "castObject" . randomDigits() . ".txt";
          file_put_contents($filename, json_encode($castObject));
echo $filename;