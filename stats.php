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
  function cmp($a, $b)
{
    return ($a->orderMarker < $b->orderMarker) ? -1 : 1;
}
usort($castObject, "cmp");
echo "<table>";
foreach ($castObject as $castMember){
    $placeOrdinal = "";
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if (($castMember->place%100) >= 11 && ($castMember->place%100) <= 13){
        
        $placeOrdinal = $castMember->place. 'th';
        
    } else {
        
        $placeOrdinal = $castMember->place. $ends[$castMember->place % 10];
    }
    
    echo "<tr>";
    echo '<td><img src="'. $castMember->image . '" height="90" width="90"></td>';
    echo '<td>' . $castMember->name . '&nbsp;&nbsp;</td>';
    echo '<td>' . $castMember->kills . " kill" . ($castMember->kills != 1 ? "s" : "") . "</td>";
    echo '<td>' . $castMember->daysAlive . " day" . ($castMember->daysAlive != 1 ? "s" : "") . " survived</td>";
    echo '<td>' . $placeOrdinal . " place</td>";
    echo '</tr>';
}
echo "</table>";
?>
    <br>
    <br>
    <button type="button" class="btn btn-primary" onclick="window.location = 'index.php'">Return to Home Screen</button>
</body>
  