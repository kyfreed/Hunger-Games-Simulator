<link rel="stylesheet" type="text/css" href="game.css?v=1.9">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
  <?php
  $castSize = $_GET['castSize'];
  ?>
<script>
    var cookie;
    function next(){
      var data = "castSize=" + <?php echo $castSize?> + "&" + $("input").filter(function(index){return $(this).val() != "";}).serialize() + "&" + $("select").serialize();
      console.log(data);
      $.ajax({
            url: "construct.php",
            async: false,
            method: "POST",
            data: data,
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
<body style="height: 100%; overflow: auto;">
    <form action="construct.php" method="post" id="castForm">
        <div style="height:100%">
            <div class="container">
        <?php
            for($i = 0; $i < $castSize; $i+=3){
        ?>
                
                        <div class="row">
                            <?php
                            for($j = 0; $j < 3; $j++){
                                if($i + $j < $castSize){
                            ?>
                            <div class="col-lg-4">
                                <strong><u>Cast member <?=$i+$j+1?></u></strong>
                                <br>
                                Name:&nbsp;
                                <input type="text" id="castName<?=$i+$j?>" name="castName<?=$i+$j?>">
                                <br>
                                Nickname:&nbsp;
                                <input type="text" id="castNick<?=$i+$j?>" name="castNick<?=$i+$j?>">
                                <br>
                                Gender:&nbsp;
                                <select id="castGender<?=$i+$j?>" name="castGender<?=$i+$j?>">
                                <option value="m">M</option>
                                <option value="f">F</option>
                                </select>
                                <br>
                                Aggression:&nbsp;
                                <select id="castDisposition<?=$i+$j?>" name="castDisposition<?=$i+$j?>">
                                    <option value="1">Very Passive</option>
                                    <option value="2">Passive</option>
                                    <option value="3" selected>Neutral</option>
                                    <option value="4">Aggressive</option>
                                    <option value="5">Very Aggressive</option>
                                </select>
                                <br>
                                Strength (1-10):&nbsp;
                                <input type="number" id="castStrength<?=$i+$j?>" name="castStrength<?=$i+$j?>">
                                <br>
                                HP:
                                <input type="number" id="castHealth<?=$i+$j?>" name="castHealth<?=$i+$j?>">
                                <br>
                                Dexterity (1-10):&nbsp;
                                <input type="number" id="castDex<?=$i+$j?>" name="castDex<?=$i+$j?>">
                                <br>
                                Intelligence (1-10):&nbsp;
                                <input type="number" id="castInt<?=$i+$j?>" name="castInt<?=$i+$j?>">
                                <br>
                                Charisma (1-10):&nbsp;
                                <input type="number" id="castCha<?=$i+$j?>" name="castCha<?=$i+$j?>">
                                <br>
                                Image URL:&nbsp;
                                <input type="text" id="castImage<?=$i+$j?>" name="castImage<?=$i+$j?>">
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