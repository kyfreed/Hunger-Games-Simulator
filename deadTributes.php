<?php
session_start();
?>
<link rel="stylesheet" type="text/css" href="deadTributes.css?v=<?= filemtime("deadTributes.css")?>">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
  <div class="text-center" style="height:100%; overflow: auto">
<title>Hunger Games Simulator</title>
<body>
<h1>Dead Tributes <?=$_COOKIE['counter']?></h1>
<?php
function print_r2($val){ //Prints an object to the page in a readable format.
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}
$deadToday = json_decode($_COOKIE['deadToday']);
$castObject = json_decode($_SESSION['castObject']);
//print_r2($castObject);
//print_r2($deadToday);
if($deadToday != array()){
    echo count($deadToday) . " cannon shot" . ((count($deadToday) == 1) ? "" : "s"). " echo" . ((count($deadToday) == 1) ? "es" : "") ." in the distance.<br><br>";
} else {
    echo "As the sun sets, the sky is silent.<br><br>";
}
foreach ($deadToday as $nick){
    foreach ($castObject as $character){
        if ($nick == $character->nick){
            if (strpos($character->image, "generateImage") !== FALSE){
                echo '<img src="generateImage.php?imageInitial=' . substr($character->image, strpos($character->image, "l=") + 2, 1) . '&r=0&g=0&b=0"><br>';
            } else {
            $image = imagecreatefrompng($character->image);
            imagefilter($image, IMG_FILTER_GRAYSCALE);
            ob_start();
            imagepng($image);
            $imagedata = ob_get_contents();
            ob_end_clean();
            echo '<img src="data:image/png;base64,'.base64_encode($imagedata).'"/ width="90" height="90"><br>';
            }
            //echo '<img src="'. $character->image . '" "width="90" height="90"><br>';
        }
    }
    echo $nick . "<br><br>";
}
?>
    <button type="button" class="btn btn-primary" onclick="document.cookie = 'deadToday=[]'; window.location = 'night.php';">Continue</button>  
</div>
</body>