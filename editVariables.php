<?php
session_start();
$_SESSION['totalDead'] = array_merge($_SESSION['totalDead'], $_SESSION['deadToday']);
$_SESSION['place'] = $_POST['place'];
if(array_key_exists("counter", $_POST)){
    $_SESSION['counter'] = $_POST['counter'];
}

