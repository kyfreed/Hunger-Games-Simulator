<?php
include_once 'utils.php';
$castObject = json_decode($_POST['cast0']);
for($i = 1; $i < count($_POST); $i++){
    $castObject = array_merge($castObject, json_decode($_POST['cast' . $i]));
}
for($i = 0; $i < count($castObject); $i++){
    $castObject[$i]->orderMarker = $i;
}
$_SESSION['castObject'] = json_encode($castObject);