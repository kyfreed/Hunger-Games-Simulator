<?php
session_start();
$castObject = $_POST['castObject'];
$_SESSION['castObject'] = $castObject;
