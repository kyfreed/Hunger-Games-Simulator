<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="game.css?v=<?= filemtime("game.css")?>">
        <script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
        <script>
            function createCast(url){
                var cookie;
                $.ajax({
                    url: "writeFile.php",
                    async: false,
                    method: "POST",
                    data: $("#castObject").serialize(),
                    dataType: "text",
                    error: function(jqXHR, textStatus, errorThrown){
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
                window.location = url;
            }
        </script>
        <title>Hunger Games Simulator</title>
    </head>
    <body style="background-color: #228C22; height: 100%;">
        <div id="pageContainer">
            <div id="contentWrap">
                <h1>Hunger Games Simulator</h1>
        <h4>Inspired by <a href="http://brantsteele.net/hungergames" target="_blank" style="color: #8C4814;
    font-family:'SkipStd-B-AlphaNum'; text-decoration: underline;">BrantSteele</a></h4>
        <form action="cast.php" method="get">
            Enter cast size to be generated:&nbsp;
            <input type="number" id="castSize" name="castSize">
            <br>
            <br>
            <button type="submit" class="btn btn-primary">Create cast</button>
        </form>
        <br>
        <p>Or, paste cast data here:</p>
            <br>
            <textarea id="castObject" name="castObject" rows="25" cols="25"></textarea>
            <br>
            <button type="button" class="btn btn-primary" onclick="createCast('castEdit.php')">Edit cast</button>
            <button type="button" class="btn btn-success" onclick="createCast('game.php')">Go!</button>
            <footer>v1.1.0.2</footer>
            </div>
        </div>
    </body>
</html>
