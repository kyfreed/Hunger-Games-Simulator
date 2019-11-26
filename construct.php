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
            $tempObject->gender = $_POST["castGender" . $i];
            $tempObject->disposition = (int) $_POST["castDisposition" . $i];
            $tempObject->strength = (int) $_POST["castStrength" . $i];
            $tempObject->maxStrength = $tempObject->strength;
            $tempObject->modifiedStrength = $tempObject->strength / 5;
            $tempObject->dexterity = (int) $_POST["castDex" . $i];
            $tempObject->intelligence = (int) $_POST["castInt" . $i];
            $tempObject->charisma = (int) $_POST["castCha" . $i];
            $tempObject->defense = 0;
            $tempObject->image = $_POST["castImage" . $i];
            $tempObject->name = htmlspecialchars($_POST["castName" . $i]);
            $tempObject->nick = htmlspecialchars($_POST["castNick" . $i]);
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
            array_push($castObject,clone $tempObject);
          }
          $filename = "castObject" . randomDigits() . ".txt";
          file_put_contents($filename, json_encode($castObject));
echo $filename;