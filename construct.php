<?php
          $castSize=count($_POST)/8;
          $castObject = [];
          for($i = 1; $i <= $castSize; $i++){
            $tempObject->gender = $_POST["castGender" . $i];
            $tempObject->disposition = (int) $_POST["castDisposition" . $i];
            $tempObject->strength = (int) $_POST["castStrength" . $i];
            $tempObject->modifiedStrength = (int) $_POST["castStrength" . $i] / 5;
            $tempObject->dexterity = (int) $_POST["castDex" . $i];
            $tempObject->intelligence = (int) $_POST["castInt" . $i];
            $tempObject->image = $_POST["castImage" . $i];
            $tempObject->name = $_POST["castName" . $i];
            $tempObject->nick = $_POST["castNick" . $i];
            $tempObject->status = "Alive";
            $tempObject->actionTaken = FALSE;
            $tempObject->daysOfFood = 0;
            $tempObject->daysOfWater = 0;
            $tempObject->desiredItems = [];
            $tempObject->inventory = [];
            array_push($castObject,clone $tempObject);
          }
echo json_encode($castObject);

