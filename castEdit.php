<?php
include('Character.php');
include_once('utils.php');
?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="game.css?<?= filemtime("game.css") ?>">
<script
    src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
crossorigin="anonymous"></script>
<?php
$castObject = $_SESSION["castObject"];
$castObjectSize = count($castObject);
$castSize = $castObjectSize;
?>
<script>
    function next() {
        var data = "castSize=" + <?php echo $castSize ?> + "&" + $("input").filter(function (index) {
            return $(this).val() != "";
        }).serialize() + "&" + $("select").serialize();
        console.log(data);
        $.ajax({
            url: "construct.php",
            async: false,
            method: "POST",
            data: data,
            dataType: "text",
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
        window.location = "game.php";
    }

    function addCharacters() {
        var castIncrease = parseInt($("#castIncrease").val());
        window.location = "/castEdit.php?castSize=" + (<?php echo $castSize ?> + castIncrease);

    }

    function deleteCharacter(i) {
        $.ajax({
            url: "deleteCharacter.php",
            async: false,
            method: "POST",
            data: "index=" + i,
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
        window.location.reload();
    }
    function exportCharacter(num) {
        $.ajax({
            url: "exportCharacter.php",
            async: false,
            method: "POST",
            data: "num=" + num + "&" + $("#character" + num + " input").filter(function (index) {
                return $(this).val() != "";
            }).serialize() + "&" + $("#character" + num + " select").serialize(),
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
        window.open("downloadCharacter.php", "_blank");
    }
</script>
<title>Hunger Games Simulator</title>
<body style="height: 100%; overflow: auto;">
    <form action="construct.php" method="post" id="castForm">
        <div style="height:100%">
            <h1 style="text-align: center;">Cast Editing</h1>
            Add&nbsp;<input type="number" id='castIncrease' maxlength="3"> characters &nbsp; <button type="button" class="btn btn-success" onclick="addCharacters()">Add</button>

            <div class="container">
                <?php
                for ($i = 0; $i < $_GET['castSize']; $i += 3) {
                    ?>

                    <div class="row">
                        <?php
                        for ($j = 0; $j < 3; $j++) {
                            if ($i + $j < $_GET['castSize']) {
                                ?>
                                <div class="col-lg-4" id="<?php echo "character" . ($i + $j) ?>">
                                    <strong><u>Cast member <?= $i + $j + 1 ?></u></strong>&nbsp;<button type="button" class="btn btn-primary" onclick="exportCharacter(<?php echo $i + $j ?>)">Export</button>&nbsp;<button type="button" class="btn btn-primary" onclick="deleteCharacter(<?php echo $i + $j ?>)">Delete</button>
                                    <br>
                                    <br>
                                    Name:&nbsp;
                                    <input type="text" id="castName<?= $i + $j ?>" name="castName<?= $i + $j ?>" value="<?php echo ($i + $j < $castObjectSize) ? $castObject[$i + $j]->name : "" ?>">
                                    <br>
                                    Nickname:&nbsp;
                                    <input type="text" id="castNick<?= $i + $j ?>" name="castNick<?= $i + $j ?>" value="<?php echo ($i + $j < $castObjectSize) ? (($castObject[$i + $j]->nick != $castObject[$i + $j]->name) ? $castObject[$i + $j]->nick : "") : "" ?>">
                                    <br>
                                    Gender:&nbsp;
                                    <select id="castGender<?= $i + $j ?>" name="castGender<?= $i + $j ?>">
                                        <option value="m" <?php echo ($i + $j < $castObjectSize) ? ($castObject[$i + $j]->gender == 'm') ? 'selected' : '' : '' ?>>M</option>
                                        <option value="f" <?php echo ($i + $j < $castObjectSize) ? ($castObject[$i + $j]->gender == 'f') ? 'selected' : '' : '' ?>>F</option>
                                    </select>
                                    <br>
                                    Aggression:&nbsp;
                                    <select id="castDisposition<?= $i + $j ?>" name="castDisposition<?= $i + $j ?>">
                                        <option value="1" <?php echo ($i + $j < $castObjectSize) ? ($castObject[$i + $j]->disposition == 1) ? 'selected' : '' : '' ?>>Very Passive</option>
                                        <option value="2" <?php echo ($i + $j < $castObjectSize) ? ($castObject[$i + $j]->disposition == 2) ? 'selected' : '' : '' ?>>Passive</option>
                                        <option value="3" <?php echo ($i + $j < $castObjectSize) ? ($castObject[$i + $j]->disposition == 3) ? 'selected' : '' : '' ?>>Neutral</option>
                                        <option value="4" <?php echo ($i + $j < $castObjectSize) ? ($castObject[$i + $j]->disposition == 4) ? 'selected' : '' : '' ?>>Aggressive</option>
                                        <option value="5" <?php echo ($i + $j < $castObjectSize) ? ($castObject[$i + $j]->disposition == 5) ? 'selected' : '' : '' ?>>Very Aggressive</option>
                                    </select>
                                    <br>
                                    Strength (1-10):&nbsp;
                                    <input type="number" id="castStrength<?= $i + $j ?>" name="castStrength<?= $i + $j ?>" value="<?php echo ($i + $j < $castObjectSize) ? (($castObject[$i + $j]->strength != 5) ? $castObject[$i + $j]->strength : "") : "" ?>">
                                    <br>
                                    HP:
                                    <input type="number" id="castHealth<?= $i + $j ?>" name="castHealth<?= $i + $j ?>" value="<?php echo ($i + $j < $castObjectSize) ? (($castObject[$i + $j]->health != 5) ? $castObject[$i + $j]->health : "") : "" ?>">
                                    <br>
                                    Dexterity (1-10):&nbsp;
                                    <input type="number" id="castDex<?= $i + $j ?>" name="castDex<?= $i + $j ?>" value="<?php echo ($i + $j < $castObjectSize) ? (($castObject[$i + $j]->dexterity != 5) ? $castObject[$i + $j]->dexterity : "") : "" ?>">
                                    <br>
                                    Intelligence (1-10):&nbsp;
                                    <input type="number" id="castInt<?= $i + $j ?>" name="castInt<?= $i + $j ?>" value="<?php echo ($i + $j < $castObjectSize) ? (($castObject[$i + $j]->intelligence != 5) ? $castObject[$i + $j]->intelligence : "") : "" ?>">
                                    <br>
                                    Charisma (1-10):&nbsp;
                                    <input type="number" id="castCha<?= $i + $j ?>" name="castCha<?= $i + $j ?>" value="<?php echo ($i + $j < $castObjectSize) ? (($castObject[$i + $j]->charisma != 5) ? $castObject[$i + $j]->charisma : "") : "" ?>">
                                    <br>
                                    Image URL:&nbsp;
                                    <input type="text" id="castImage<?= $i + $j ?>" name="castImage<?= $i + $j ?>" value="<?php echo ($i + $j < $castObjectSize) ? ((strpos($castObject[$i + $j]->image, "generateImage.php") === FALSE) ? $castObject[$i + $j]->image : "") : "" ?>">
                                    <br>
                                    <br>
                                </div>
                                <?php
                            }
                        }
                        if ($i < $castObjectSize - 3) {
                            ?>
                        </div>
                        <?php
                    }
                }
                $startValue = $castObjectSize;
                $fillCounter = 0;
                if ($castObjectSize % 3 > 0 && $castSize - $castObjectSize > 0) {
                    for ($i = $castObjectSize; $i < $castObjectSize + 3 - ($castObjectSize % 3); $i++) {
                        $startValue++;
                        $fillCounter++;
                        ?>
                        <div class="col-lg-4" id="<?php echo "character" . ($i) ?>">
                            <strong><u>Cast member <?= $i + 1 ?></u></strong>&nbsp;<button type="button" class="btn btn-primary" onclick="exportCharacter(<?php echo $i ?>)">Export</button>&nbsp;<button type="button" class="btn btn-primary" onclick="deleteCharacter(<?php echo $i ?>)">Delete</button>
                            <br>
                            <br>
                            Name:&nbsp;
                            <input type="text" id="castName<?= $i ?>" name="castName<?= $i ?>">
                            <br>
                            Nickname:&nbsp;
                            <input type="text" id="castNick<?= $i ?>" name="castNick<?= $i ?>">
                            <br>
                            Gender:&nbsp;
                            <select id="castGender<?= $i ?>" name="castGender<?= $i ?>">
                                <option value="m">M</option>
                                <option value="f">F</option>
                            </select>
                            <br>
                            Aggression:&nbsp;
                            <select id="castDisposition<?= $i ?>" name="castDisposition<?= $i ?>">
                                <option value="1">Very Passive</option>
                                <option value="2">Passive</option>
                                <option value="3" selected>Neutral</option>
                                <option value="4">Aggressive</option>
                                <option value="5">Very Aggressive</option>
                            </select>
                            <br>
                            Strength (1-10):&nbsp;
                            <input type="number" id="castStrength<?= $i ?>" name="castStrength<?= $i ?>" min="1" max="10">
                            <br>
                            HP:
                            <input type="number" id="castHealth<?= $i ?>" name="castHealth<?= $i ?>" min="1" max="9999">
                            <br>
                            Dexterity (1-10):&nbsp;
                            <input type="number" id="castDex<?= $i ?>" name="castDex<?= $i ?>" min="1" max="10">
                            <br>
                            Intelligence (1-10):&nbsp;
                            <input type="number" id="castInt<?= $i ?>" name="castInt<?= $i ?>" min="1" max="10">
                            <br>
                            Charisma (1-10):&nbsp;
                            <input type="number" id="castCha<?= $i ?>" name="castCha<?= $i ?>" min="1" max="10">
                            <br>
                            Image URL:&nbsp;
                            <input type="text" id="castImage<?= $i ?>" name="castImage<?= $i ?>">
                            <br>
                            <br>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <?php
            for ($i = $startValue; $i < $startValue + $castSize - $castObjectSize - $fillCounter; $i += 3) {
                ?>
                <div class="row">
                    <?php
                    for ($j = 0; $j < 3; $j++) {
                        if ($i + $j < $startValue + $castSize - $castObjectSize - $fillCounter) {
                            ?>
                            <div class="col-lg-4" id="<?php echo "character" . ($i + $j) ?>">
                                <strong><u>Cast member <?= $i + $j + 1 ?></u></strong>&nbsp;<button type="button" class="btn btn-primary" onclick="exportCharacter(<?php echo $i + $j ?>)">Export</button>&nbsp;<button type="button" class="btn btn-primary" onclick="deleteCharacter(<?php echo $i + $j ?>)">Delete</button>
                                <br>
                                <br>
                                Name:&nbsp;
                                <input type="text" id="castName<?= $i + $j ?>" name="castName<?= $i + $j ?>">
                                <br>
                                Nickname:&nbsp;
                                <input type="text" id="castNick<?= $i + $j ?>" name="castNick<?= $i + $j ?>">
                                <br>
                                Gender:&nbsp;
                                <select id="castGender<?= $i + $j ?>" name="castGender<?= $i + $j ?>">
                                    <option value="m">M</option>
                                    <option value="f">F</option>
                                </select>
                                <br>
                                Aggression:&nbsp;
                                <select id="castDisposition<?= $i + $j ?>" name="castDisposition<?= $i + $j ?>">
                                    <option value="1">Very Passive</option>
                                    <option value="2">Passive</option>
                                    <option value="3" selected>Neutral</option>
                                    <option value="4">Aggressive</option>
                                    <option value="5">Very Aggressive</option>
                                </select>
                                <br>
                                Strength (1-10):&nbsp;
                                <input type="number" id="castStrength<?= $i + $j ?>" name="castStrength<?= $i + $j ?>" min="1" max="10">
                                <br>
                                HP:
                                <input type="number" id="castHealth<?= $i + $j ?>" name="castHealth<?= $i + $j ?>" min="1" max="9999">
                                <br>
                                Dexterity (1-10):&nbsp;
                                <input type="number" id="castDex<?= $i + $j ?>" name="castDex<?= $i + $j ?>" min="1" max="10">
                                <br>
                                Intelligence (1-10):&nbsp;
                                <input type="number" id="castInt<?= $i + $j ?>" name="castInt<?= $i + $j ?>" min="1" max="10">
                                <br>
                                Charisma (1-10):&nbsp;
                                <input type="number" id="castCha<?= $i + $j ?>" name="castCha<?= $i + $j ?>" min="1" max="10">
                                <br>
                                Image URL:&nbsp;
                                <input type="text" id="castImage<?= $i + $j ?>" name="castImage<?= $i + $j ?>">
                                <br>
                                <br>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <?php
            }
            ?>

            <div class="text-center">
                <button type="button" class="btn btn-primary" onclick="next()">Submit</button>
            </div>
        </div>
    </div>
</form>
</body>