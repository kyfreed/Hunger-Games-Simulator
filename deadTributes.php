<?php
include('Character.php');
include_once('utils.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="deadTributes.css?v=<?= filemtime("deadTributes.css") ?>">
<script
    src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
crossorigin="anonymous"></script>
<div class="text-center" style="height:100%; overflow: auto">
    <title>Hunger Games Simulator</title>
    <body>
        <h1>Dead Tributes <?= $_SESSION['counter'] ?></h1>
        <?php
        $deadToday = $_SESSION['totalDead'];
        $castObject = $_SESSION['castObject'];
        
        if ($deadToday != array()) {
            echo count($deadToday) . " cannon shot" . ((count($deadToday) == 1) ? "" : "s") . " echo" . ((count($deadToday) == 1) ? "es" : "") . " in the distance.<br><br>";
        } else {
            echo "As the sun sets, the sky is silent.<br><br>";
        }
        foreach ($deadToday as $nick) {
            foreach ($castObject as $character) {
                if ($nick == $character->nick) {
                    if (strpos($character->image, "generateImage") !== FALSE) {
                        echo '<img src="generateImage.php?imageInitial=' . substr($character->image, strpos($character->image, "l=") + 2, 1) . '&r=0&g=0&b=0"><br>';
                    } else {
                        $image = imagecreatefrompng($character->image);
                        imagefilter($image, IMG_FILTER_GRAYSCALE);
                        ob_start();
                        imagepng($image);
                        $imagedata = ob_get_contents();
                        ob_end_clean();
                        echo '<img src="data:image/png;base64,' . base64_encode($imagedata) . '"/ width="90" height="90"><br>';
                    }
                    //echo '<img src="'. $character->image . '" "width="90" height="90"><br>';
                }
            }
            echo $nick . "<br><br>";
        }
        ?>
        <button type="button" class="btn btn-primary" onclick="next();">Continue</button>  
</div>
<script>
    function next() {
        $.ajax({
            url: "clearDead.php",
            async: false,
            method: "POST",
        });
        window.location = 'night.php';
    }
</script>
</body>