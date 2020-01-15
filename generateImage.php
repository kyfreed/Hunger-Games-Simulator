<?php
header('Content-type:image/png');
$imageInitial = $_GET["imageInitial"];
$size = imagettfbbox(72, 0, "sourcecodeprosemibold.ttf", $imageInitial);
$x = round((90 - $size[2]) / 2) - 3;
$y = round((90 - $size[5]) / 2);
$img = imagecreate(90, 90);
$background = imagecolorallocate( $img, 255, 255, 255 );
$text_colour = imagecolorallocate( $img, 0, 0, 0 );
imagettftext($img, 72, 0, $x, $y, $text_colour, "sourcecodeprosemibold.ttf", $imageInitial);
echo imagepng($img);
