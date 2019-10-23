<link rel="stylesheet" type="text/css" href="index.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">  
<?php
  function print_r2($val){ //Prints an object to the page in a readable format.
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}
  
      $castSize=count($_POST)/6;
      $castObject = [];
      for($i = 1; $i <= $castSize; $i++){
        $tempObject->gender = $_POST["castGender" . $i];
        $tempObject->disposition = (int) $_POST["castDisposition" . $i];
        $tempObject->strength = (int) $_POST["castStrength" . $i];
        $tempObject->image = $_POST["castImage" . $i];
        $tempObject->name = $_POST["castName" . $i];
        $tempObject->nick = $_POST["castNick" . $i];
        $tempObject->status = "Alive";
        $tempObject->actionTaken = False;
        $tempObject->daysOfFood = 0;
        $tempObject->daysOfWater = 0;
        $tempObject->desiredItems = [];
        $tempObject->inventory = [];
        array_push($castObject,clone $tempObject);
    }
    setcookie("castObject", json_encode($castObject));
?>
  <body>
    <div class="text-center">
        <h1>Cast</h1>
    </div>
    <div class="container">
        <div class="row">
    <?php
    $counter = -1;
    foreach ($castObject as $value) {
        $counter++;
        if($counter % 3 == 0){
            ?>
        </div>
        <div class="row">
        <?php
        }
        ?>
            <div class="col-lg-4">
                <div class="text-center">
                    <img src="<?=$value->image?>" width="90" height="90">
                <br>
                <strong><?=$value->name?></strong>
                <br>
                <strong><?=$value->status?></strong>
                <br>
                <br>
                </div>
            </div>
        <?php
    }
    ?>
        </div>
    </div>
  <div class="text-center">
      <button type="button" class="btn btn-primary" onclick="window.location = 'bloodbath.php';">Start</button>
  </div>
  </body>