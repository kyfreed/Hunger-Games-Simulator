<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="stats.css?v=<?= filemtime('stats.css') ?>">
<script
src="https://code.jquery.com/jquery-3.4.1.min.js"
integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
crossorigin="anonymous"></script>
  <title>Hunger Games Simulator</title>
  <body>
      <h1 style="text-align: center;">Final Stats</h1>
  <?php
  $castObject = json_decode(file_get_contents($_COOKIE['castObjectFile']));
  function cmp($a, $b){
      return strcmp($a->name, $b->name);
  }
  usort($castObject, "cmp");
  
  function maxNameLength($castObject){
      $maxLength = 0;
      foreach($castObject as $castMember){
          if(strlen($castMember->name) > $maxLength){
              $maxLength = strlen($castMember->name);
          }
      }
      return $maxLength;
  }
  $nameLength = maxNameLength($castObject);
  foreach ($castObject as $castMember){
      echo '<img src="'. $castMember->image . '" height="90" width="90">&nbsp;';
      echo $castMember->name . str_repeat("&nbsp;", ($nameLength - strlen($castMember->name)) + 2) . $castMember->kills . " kill" . ($castMember->kills != 1 ? "s" : "") . "<br><br>";
  }
  ?>
      <br>
      <br>
      <button type="button" class="btn btn-primary" onclick="window.location = 'index.php'">Return to Home Screen</button>
  </body>
  