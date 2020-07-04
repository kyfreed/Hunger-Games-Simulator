<?php
include_once 'utils.php';
$castObject = [];
for($i = 0; $i < count($_POST); $i++){
    array_push($castObject, json_decode($_POST['char' . $i]));
}
for($i = 0; $i < count($castObject); $i++){
    $castObject[$i]->orderMarker = $i;
}
$_SESSION['castObject'] = json_encode($castObject);
