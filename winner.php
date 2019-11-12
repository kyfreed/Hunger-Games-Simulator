<link rel="stylesheet" type="text/css" href="game.css?v=1.6">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
        <script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
  <title>Hunger Games Simulator</title>
  <div class="text-center" style="height: 100%;">
      <h1>The Winner</h1>
<?php
$castObject = json_decode(file_get_contents($_COOKIE['castObjectFile']));
foreach ($castObject as $character) {
    if($character->status == "Alive"){
        $winner = $character;
    }
}
echo "<img src='$winner->image' width='90' height='90'><br>";
echo $winner->nick . " is the winner!<br><br>";
?>
      <button type="button" class="btn btn-primary" onclick="window.location = 'index.php'">Return to Home Screen</button>
  </div>