<link rel="stylesheet" type="text/css" href="index.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<form>
<?php
    $castSize = $_GET['castSize'];
    for($i = 1; $i <= $castSize; $i++){
       ?>
    <strong><u>Cast member <?=$i?></u></strong>
    <br>
    Name:&nbsp;
    <input type="text" name="castName<?=$i?>">
    <br>
    Gender:&nbsp;
    <select name="castGender<?=$i?>">
        <option value="m">M</option>
        <option value="f">F</option>
    </select>
    <br>
    <br>
<?php
    }
?>
</form>
