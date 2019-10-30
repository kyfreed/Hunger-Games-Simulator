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
            $tempObject->modifiedStrength = (int) $_POST["castStrength" . $i] / 5;
            $tempObject->dexterity = (int) $_POST["castDex" . $i];
            $tempObject->intelligence = (int) $_POST["castInt" . $i];
            $tempObject->charisma = (int) $_POST["castCha" . $i];
            $tempObject->defense = 0;
            $tempObject->image = $_POST["castImage" . $i];
            $tempObject->name = $_POST["castName" . $i];
            $tempObject->nick = $_POST["castNick" . $i];
            $tempObject->status = "Alive";
            $tempObject->actionTaken = FALSE;
            $tempObject->daysOfFood = 1;
            $tempObject->daysOfWater = 1;
            $tempObject->desiredItems = [];
            $tempObject->inventory = [];
            array_push($castObject,clone $tempObject);
          }
          $filename = "castObject" . randomDigits() . ".txt";
          file_put_contents($filename, json_encode($castObject));
echo $filename;