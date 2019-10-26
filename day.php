        <link rel="stylesheet" type="text/css" href="index.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
        <script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
<?php
function print_r2($val){ //Prints an object to the page in a readable format.
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}
$castObject = json_decode($_COOKIE['castObject']);
$castSize = count($castObject);
function hasDuplicateHighs($array){ //Checks if an array has two or more high values.
        $dupe_array = array();
        foreach ($array as $val) {
            if (++$dupe_array[$val] > 1 && max($array) == $val) {
                return true;
            }
        }
        return false;
      }
function strongestCharacter($characters){ //In an array of characters, return the strongest one.
    $strongestCharacter = $characters[0];
    foreach($characters as $character){
        if($character->modifiedStrength > $strongestCharacter->modifiedStrength){
            $strongestCharacter = $character;
        }
    }
    return $strongestCharacter;
}
print_r2($castObject);


