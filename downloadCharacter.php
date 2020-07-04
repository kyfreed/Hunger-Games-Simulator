<?php
include_once 'utils.php';
header("Content-type: text/plain");
header('Content-Disposition: attachment; filename="' . json_decode($_SESSION['character'])->name . '.txt"');
print $_SESSION['character'];
exit();

