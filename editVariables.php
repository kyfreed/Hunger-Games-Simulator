<?php
array_merge($_SESSION['totalDead'], json_decode($_POST['deadToday']));
$_SESSION['place'] = $_POST['place'];
if(array_key_exists("counter", $_POST)){
    $_SESSION['counter'] = $_POST['counter'];
}

