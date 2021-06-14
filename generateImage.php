<?php
header ('Content-Type: image/png');
$imageInitial = $_GET["imageInitial"];
$r = $_GET['r'];
$g = $_GET['g'];
$b = $_GET['b'];
$size = imagettfbbox(72, 0, "./sourcecodeprosemibold.ttf", $imageInitial);
$x = round((90 - $size[2]) / 2);
$y = round((90 - $size[5]) / 2);
$im = @imagecreatetruecolor(90, 90)
      or die('Cannot Initialize new GD image stream');
$background = imagecolorallocate( $im, $r, $g, $b );
imagefill($im, 0, 0, $background);
$percentRGB = [$r / 255, $g / 255, $b /255];
if(sqrt(0.299 * ($percentRGB[0] ** 2) + 0.587 * ($percentRGB[1] ** 2) + 0.114 * ($percentRGB[2] ** 2)) > 0.179){
    $text_colour = imagecolorallocate( $im, 0, 0, 0 );
} else {
    $text_colour = imagecolorallocate( $im, 255, 255, 255 );
}
imagettftext($im, 72, 0, $x, $y, $text_colour, "./sourcecodeprosemibold.ttf", $imageInitial);
imagepng($im);
imagedestroy($im);
?>
