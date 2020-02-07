<?php
session_start();
header("Content-type: text/plain");
header('Content-Disposition: attachment; filename="' . json_decode($_SESSION['character'])->name . '.txt"');
print $_SESSION['character'];
exit();

