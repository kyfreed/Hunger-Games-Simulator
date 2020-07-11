<?php

include_once 'utils.php';
$castObject = $_SESSION['castObject'];
$index = (int) $_POST['index'];
unset($castObject[$index]);
$castObject = array_values($castObject);
$_SESSION['castObject'] = json_encode($castObject);
