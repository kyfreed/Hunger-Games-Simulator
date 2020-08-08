<?php
include('Character.php');
include_once('utils.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="game.css?v=<?= filemtime("game.css") ?>">
<script
    src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
crossorigin="anonymous"></script>
<?php
$castObject = $_SESSION['castObject'];
$necessaryCastData = [];
$compressedCharacter = new stdClass();
foreach ($castObject as $character){
    $compressedCharacter->name = $character->name;
    $compressedCharacter->nick = $character->nick;
    $compressedCharacter->gender = $character->gender;
    $compressedCharacter->disposition = $character->disposition;
    $compressedCharacter->strength = $character->strength;
    $compressedCharacter->health = $character->health;
    $compressedCharacter->dexterity = $character->dexterity;
    $compressedCharacter->intelligence = $character->intelligence;
    $compressedCharacter->charisma = $character->charisma;
    $compressedCharacter->image = $character->image;
    $compressedCharacter->orderMarker = $character->orderMarker;
    array_push($necessaryCastData, json_encode(clone $compressedCharacter));
}
$castSize = count($castObject);
?>
<script>
    function showCastData() {
        $("#castObject").val("[" + <?= json_encode($necessaryCastData) ?> + "]");
    }
    function next() {
        window.location = 'bloodbath.php';
    }
</script>
<title>Hunger Games Simulator</title>
<body>
    <div class="text-center">
        <h1>Cast</h1>
        <div class="container">
            <div class="row">
                <?php
                $counter = -1;
                foreach ($castObject as $value) {
                    $counter++;
                    if ($counter % 3 == 0) {
                        ?>
                    </div>
                    <div class="row">
                        <?php
                    }
                    ?>
                    <div class="col-lg-4">

                        <img src="<?= $value->image ?>" width="90" height="90">
                        <br>
                        <strong><?= $value->name ?></strong>
                        <br>
                        <strong><?= $value->status ?></strong>
                        <br>
                        <br>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <button type="button" class="btn btn-primary" onclick="next()">Start</button>
        <br>
        <br>
        <textarea id="castObject" rows="25" cols="25" readonly></textarea>
        <br>
        <br>
        <button type="button" class="btn btn-primary" onclick="showCastData()">Show cast data</button>
    </div>
</body>