<?php
session_start();
//function print_r2($val){ //Prints an object to the page in a readable format.
//        echo '<pre>';
//        print_r($val);
//        echo  '</pre>';
//}
//print_r2($_REQUEST);
$_SESSION['castObject'] = $_POST['castObject'];
echo $_POST;
