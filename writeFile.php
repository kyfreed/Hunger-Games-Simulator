<?php
$castObject = $_POST['castObject'];
function randomDigits(){
              do{
                  clearstatcache();
                $digits = "";
                for($i = 0; $i < 12; $i++){
                  $digits .= round(rand(0,9));
                }
              } while(file_exists("castObject" . $digits . ".txt") === TRUE);
              return $digits;
          }
$filename = "castObject" . randomDigits() . ".txt";
file_put_contents($filename, $castObject);
echo $filename;
