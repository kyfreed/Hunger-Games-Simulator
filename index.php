<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"
              integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href='game.css?v=<?= filemtime("game.css") ?>'>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script>
            $(document).ready(function () {
                $("#castNumber").val("0");
                $("#charNumber").val("0");
                $('#flip').click(function () {
                    if ($('#arrow').is('.glyphicon-chevron-down')) {
                        $('#arrow').removeClass('glyphicon-chevron-down');
                        $('#arrow').addClass('glyphicon-chevron-up');
                    } else {
                        $('#arrow').removeClass('glyphicon-chevron-up');
                        $('#arrow').addClass('glyphicon-chevron-down');
                    }
                    $('#panel').slideToggle();
                });

                $('#castNumber').change(function () {
                    var str = '';
                    for (var i = 0; i < parseInt($('#castNumber').val()); i += 3) {
                        str += '<div class="row">';
                        for (var j = 0; j < 3; j++) {
                            if (i + j < parseInt($('#castNumber').val())) {
                                str += '<div class="col-lg-4"><textarea id="cast' + (i + j) + '" class="castMerge" rows="25" cols="25"></textarea></div>';
                            }
                        }
                        str += '</div>';
                    }
                    document.getElementById('castInput').innerHTML = str;
                });
                $('#charNumber').change(function () {
                    var str = '';
                    for (var i = 0; i < parseInt($('#charNumber').val()); i += 3) {
                        str += '<div class="row">';
                        for (var j = 0; j < 3; j++) {
                            if (i + j < parseInt($('#charNumber').val())) {
                                str += '<div class="col-lg-4"><textarea id="char' + (i + j) + '" class="charImport" rows="25" cols="25"></textarea></div>';
                            }
                        }
                        str += '</div>';
                    }
                    document.getElementById('charInput').innerHTML = str;
                });
            });
            function createCast(url) {
                var cookie;
                $.ajax({
                    url: "writeFile.php",
                    async: false,
                    method: "POST",
                    data: $("#castObject").serialize(),
                    dataType: "text",
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
                window.location = url;
            }
            function mergeCast(url) {
                var data = '';
                $(".castMerge").each(function (index, element) {
                    data += $(this).attr("id") + "=" + $(this).val();
                    if (index < $('.castMerge').length - 1) {
                        data += "&";
                    }
                });
                $.ajax({
                    url: "castMerge.php",
                    async: false,
                    method: "POST",
                    data: data,
                    dataType: "text",
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
                window.location = url;
            }
            function importCharacters(url) {
                var data = '';
                $(".charImport").each(function (index, element) {
                    data += $(this).attr("id") + "=" + $(this).val();
                    if (index < $('.charImport').length - 1) {
                        data += "&";
                    }
                });
                $.ajax({
                    url: "importCharacters.php",
                    async: false,
                    method: "POST",
                    data: data,
                    dataType: "text",
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
                window.location = url;
            }
        </script>
        <title>Hunger Games Simulator</title>
    </head>
    <body style='background-color: #228C22; height: 100%;'>
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
                <br>
                <p>Or, paste cast data here:</p>
                <br>
                <textarea id="castObject" name="castObject" rows="25" cols="25"></textarea>
                <br>
                <button type="button" class="btn btn-primary" onclick="createCast('castEdit.php')">Edit cast</button>
                <button type="button" class="btn btn-success" onclick="createCast('game.php')">Go!</button>
                <br>
                <br>
                <span id="flip" style="cursor: pointer">More &nbsp; <span class="glyphicon glyphicon-chevron-down" id="arrow"></span></span>
                <div id="panel" hidden>
                    Merge &nbsp; <input type="number" id="castNumber"> &nbsp; casts
                    &nbsp; <button class="btn btn-primary" onclick="mergeCast('castEdit.php')">Edit cast</button>
                    &nbsp; <button class="btn btn-success" onclick="mergeCast('game.php')">Go!</button>
                    <br>
                    <div id="castInput" class="container"></div>
                    <br>
                    <br>
                    Import &nbsp; <input type="number" id="charNumber"> &nbsp; characters
                    &nbsp; <button class="btn btn-primary" onclick="importCharacters('castEdit.php')">Edit cast</button>
                    &nbsp; <button class="btn btn-success" onclick="importCharacters('game.php')">Go!</button>
                    <br>
                    <div id="charInput" class="container"></div>
                </div>
                <footer>v1.2.4</footer>
            </div>
        </div>
    </body>
</html>
