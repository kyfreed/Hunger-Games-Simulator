<?php

include_once 'utils.php';
$_SESSION['castObject'] = $_SESSION['castObjectToday'];
$_SESSION['totalDead'] = array_merge($_SESSION['totalDead'], $_SESSION['deadToday']);
$_SESSION['place'] = $_SESSION['placeToday'];
if (array_key_exists("counter", $_POST)) {
    $_SESSION['counter'] = $_POST['counter'];
}

