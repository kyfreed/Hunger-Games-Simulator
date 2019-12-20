<?php
function startsWith ($string, $startString) 
{ 
    $len = strlen($startString); 
    return (substr($string, 0, $len) === $startString); 
}

function endsWith($string, $endString) 
{ 
    $len = strlen($endString); 
    if ($len == 0) { 
        return true; 
    } 
    return (substr($string, -$len) === $endString); 
}

$files = scandir("..");

foreach ($files as $file){
    if(startsWith($file, "castObject") && time() - filemtime($file) >= 604800){
        unlink($file);
    }
}

