<?php
session_start();
function print_r2($val){ //Prints an object to the page in a readable format.
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}
$post = file_get_contents('php://input');
print_r2($post);
$_SESSION['castObject'] = $post;
