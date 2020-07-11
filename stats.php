<?php
include 'Character.php';
include_once 'utils.php';
?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="stats.css?v=<?= filemtime('stats.css') ?>">
<script
    src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
crossorigin="anonymous"></script>
<script>
    $(document).ready(function () {
        $("#sortBy").val("<?php echo $_GET['sortBy'] ?>");
        $("#sortBy").change(function () {
            window.location = "/stats.php?sortBy=" + $("#sortBy").val();
        });
    });
</script>
<title>Hunger Games Simulator</title>
<body>
    <h1 style="text-align: center;">Final Stats</h1>
    Sort by &nbsp;<select id="sortBy" style="background-color: #000;">
        <option value="orderMarker">original order</option>
        <option value="place">place</option>
        <option value="kills">number of kills</option>
        <option value="daysAlive">days survived</option>
    </select>
    <?php
    $castObject = $_SESSION['castObject'];

    function cmp($a, $b) {
        switch ($_GET['sortBy']) {
            case "orderMarker":
                return ($a->orderMarker < $b->orderMarker) ? -1 : 1;
                break;
            case "place":
                if ($a->place == $b->place) {
                    return ($a->orderMarker < $b->orderMarker) ? -1 : 1;
                } else {
                    return ($a->place < $b->place) ? -1 : 1;
                }
                break;
            case "kills":
                if ($a->kills == $b->kills) {
                    return ($a->orderMarker < $b->orderMarker) ? -1 : 1;
                } else {
                    return ($a->kills > $b->kills) ? -1 : 1;
                }
                break;
            case "daysAlive":
                if ($a->daysAlive == $b->daysAlive) {
                    return ($a->orderMarker < $b->orderMarker) ? -1 : 1;
                } else {
                    return ($a->daysAlive > $b->daysAlive) ? -1 : 1;
                }
        }
    }

    usort($castObject, "cmp");
    echo "<table>";
    foreach ($castObject as $castMember) {
        $placeOrdinal = "";
        $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
        if (($castMember->place % 100) >= 11 && ($castMember->place % 100) <= 13) {

            $placeOrdinal = $castMember->place . 'th';
        } else {

            $placeOrdinal = $castMember->place . $ends[$castMember->place % 10];
        }

        echo "<tr>";
        echo '<td><img src="' . $castMember->image . '" height="90" width="90"></td>';
        echo '<td>' . $castMember->name . '&nbsp;&nbsp;</td>';
        echo '<td>' . $castMember->kills . " kill" . ($castMember->kills != 1 ? "s" : "") . "</td>";
        echo '<td>' . $castMember->daysAlive . " day" . ($castMember->daysAlive != 1 ? "s" : "") . " survived</td>";
        echo '<td>' . $placeOrdinal . " place</td>";
        if ($castMember->killedBy != "") {
            echo '<td>Killed by ' . $castMember->killedBy . '</td>';
        } else {
            echo '<td></td>';
        }
        echo '</tr>';
    }
    echo "</table>";
    ?>
    <br>
    <br>
    <button type="button" class="btn btn-primary" onclick="window.location = 'index.php'">Return to Home Screen</button>
</body>
