<?php
function startsWith ($string, $startString) 
{ 
    $len = strlen($startString); 
    return (substr($string, 0, $len) === $startString); 
}

$files = scandir(".");
$deletedFiles = [];

foreach ($files as $file){
    if(startsWith($file, "castObject") && time() - filemtime($file) >= 604800){
        $deletedFiles->append($file);
        unlink($file);
    }
}

echo $deletedFiles;

