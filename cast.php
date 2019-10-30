<link rel="stylesheet" type="text/css" href="index.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
  <script>
      var cookie;
      function next(){
          $.ajax({
              url: "construct.php",
              async: false,
              method: "POST",
              data: $("#castForm").serialize(),
              dataType: "text",
              success: function(castCookie){
                  cookie = castCookie;
              },
              error: function(jqXHR, textStatus, errorThrown){
                  console.log(textStatus);
                  console.log(errorThrown);
              }
          });
          document.cookie = "castObjectFile=" + cookie;
          window.location = "game.php";
      }
  </script>
  <title>Hunger Games Simulator</title>
<form action="construct.php" method="post" id="castForm">
<?php
    $castSize = $_GET['castSize'];
    for($i = 1; $i <= $castSize; $i+=3){
       ?>
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <strong><u>Cast member <?=$i?></u></strong>
                <br>
                Name:&nbsp;
                <input type="text" id=castName<?=$i?>" name="castName<?=$i?>">
                <br>
                Nickname:&nbsp;
                <input type="text" id=castNick<?=$i?>" name="castNick<?=$i?>">
                <br>
                Gender:&nbsp;
                <select name="castGender<?=$i?>">
                <option value="m">M</option>
                <option value="f">F</option>
                </select>
                <br>
                Disposition:&nbsp;
                <select name="castDisposition<?=$i?>">
                    <option value="1">Very Passive</option>
                    <option value="2">Passive</option>
                    <option value="3" selected>Neutral</option>
                    <option value="4">Aggressive</option>
                    <option value="5">Very Aggressive</option>
                </select>
                <br>
                Strength (1-10):&nbsp;
                <input type="number" name="castStrength<?=$i?>">
                <br>
                Dexterity (1-10):&nbsp;
                <input type="number" name="castDex<?=$i?>">
                <br>
                Intelligence (1-10):&nbsp;
                <input type="number" name="castInt<?=$i?>">
                <br>
                Charisma (1-10):&nbsp;
                <input type="number" name="castCha<?=$i?>">
                <br>
                Image URL:&nbsp;
                <input type="text" name="castImage<?=$i?>">
                <br>
                <br>
            </div>
            <div class="col-lg-4">
                <?php
                if($i + 1 <= $castSize){
                    ?>
                <strong><u>Cast member <?=$i+1?></u></strong>
                <br>
                Name:&nbsp;
                <input type="text" id=castName<?=$i+1?>" name="castName<?=$i+1?>">
                <br>
                Nickname:&nbsp;
                <input type="text" id=castNick<?=$i+1?>" name="castNick<?=$i+1?>">
                <br>
                Gender:&nbsp;
                <select name="castGender<?=$i+1?>">
                <option value="m">M</option>
                <option value="f">F</option>
                </select>
                <br>
                Disposition:&nbsp;
                <select name="castDisposition<?=$i+1?>">
                    <option value="1">Very Passive</option>
                    <option value="2">Passive</option>
                    <option value="3" selected>Neutral</option>
                    <option value="4">Aggressive</option>
                    <option value="5">Very Aggressive</option>
                </select>
                <br>
                Strength (1-10):&nbsp;
                <input type="number" name="castStrength<?=$i+1?>">
                <br>
                Dexterity (1-10):&nbsp;
                <input type="number" name="castDex<?=$i+1?>">
                <br>
                Intelligence (1-10):&nbsp;
                <input type="number" name="castInt<?=$i+1?>">
                <br>
                Charisma (1-10):&nbsp;
                <input type="number" name="castCha<?=$i+1?>">
                <br>
                Image URL:&nbsp;
                <input type="text" name="castImage<?=$i+1?>"
                <br>
                <br>
                <?php
                }
                ?>
            </div>
            <div class="col-lg-4">
                <?php
                if($i + 2 <= $castSize){
                    ?>
                <strong><u>Cast member <?=$i+2?></u></strong>
                <br>
                Name:&nbsp;
                <input type="text" id=castName<?=$i+2?>" name="castName<?=$i+2?>">
                <br>
                Nickname:&nbsp;
                <input type="text" id=castNick<?=$i+2?>" name="castNick<?=$i+2?>">
                <br>
                Gender:&nbsp;
                <select name="castGender<?=$i+2?>">
                <option value="m">M</option>
                <option value="f">F</option>
                </select>
                <br>
                Disposition:&nbsp;
                <select name="castDisposition<?=$i+2?>">
                    <option value="1">Very Passive</option>
                    <option value="2">Passive</option>
                    <option value="3" selected>Neutral</option>
                    <option value="4">Aggressive</option>
                    <option value="5">Very Aggressive</option>
                </select>
                <br>
                Strength (1-10):&nbsp;
                <input type="number" name="castStrength<?=$i+2?>">
                <br>
                Dexterity (1-10):&nbsp;
                <input type="number" name="castDex<?=$i+2?>">
                <br>
                Intelligence (1-10):&nbsp;
                <input type="number" name="castInt<?=$i+2?>">
                <br>
                Charisma (1-10):&nbsp;
                <input type="number" name="castCha<?=$i+2?>">
                <br>
                Image URL:&nbsp;
                <input type="text" name="castImage<?=$i+2?>">
                <br>
                <br>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
<?php
    }
?>
    <div class="text-center">
        <button type="button" class="btn btn-primary" onclick="next()">Submit</button>
    </div>
</form>
