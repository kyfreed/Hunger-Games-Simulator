<?php
session_start();
$castObject = $_POST['castObject'];
$_SESSION['castObject'] = $castObject;
$_SESSION['totalDead'] = [];
$_SESSION['counter'] = 1;
$_SESSION['place'] = count(json_decode($castObject));