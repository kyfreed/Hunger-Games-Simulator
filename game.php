<link rel="stylesheet" type="text/css" href="index.css?v=1.4">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
<script>
    function showCastData(){
            $("#castObject").val(<?= json_encode(file_get_contents($_COOKIE['castObjectFile']))?>);
    }
</script>
<title>Hunger Games Simulator</title>
<?php
  function print_r2($val){ //Prints an object to the page in a readable format.
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}
          $castObject = json_decode(file_get_contents($_COOKIE['castObjectFile']));
          $castSize = count($castObject);
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
      <br>
      <br>
      <textarea id="castObject" rows="25" cols="25" readonly></textarea>
      <br>
      <br>
      <button type="button" class="btn btn-primary" onclick="showCastData()">Show cast data</button>
  </div>
  </body>