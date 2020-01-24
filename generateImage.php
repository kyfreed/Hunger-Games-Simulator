<?php
header('Content-type:image/png');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$imageInitial = $_GET["imageInitial"];
$r = $_GET['r'];
$g = $_GET['g'];
$b = $_GET['b'];
function pickTextColorBasedOnBgColorSimple($bgColor, $lightColor, $darkColor) {
  $color = (substr($bgColor,0, 1) === '#') ? substr($bgColor, 1) : $bgColor;
  $r = hexdec(substr($color, 0, 2)); // hexToR
  $g = hexdec(substr($color, 2, 2)); // hexToG
  $b = hexdec(substr($color, 4, 2)); // hexToB
  return ((($r * 0.299) + ($g * 0.587) + ($b * 0.114)) > 186) ?
    $darkColor : $lightColor;
}
function pickTextColorBasedOnBgColorAdvanced($bgColor, $lightColor, $darkColor) {
  $color = (substr($bgColor,0, 1) === '#') ? substr($bgColor, 1) : $bgColor;
  $r = hexdec(substr($color, 0, 2)); // hexToR
  $g = hexdec(substr($color, 2, 2)); // hexToG
  $b = hexdec(substr($color, 4, 2)); // hexToB
  $uicolors = [$r / 255, $g / 255, $b / 255];
  $c = [];
  for($i = 0; $i < 3; $i++){
      if($uicolors[$i] <= 0.03928){
          $c[$i] = $uicolors[$i] / 12.92;
      } else {
          $c[$i] = (($uicolors[$i] + 0.055) / 1.055) ** 2.4; 
      }
  }
//  var c = uicolors.map((col) => {
//    if (col <= 0.03928) {
//      return col / 12.92;
//    }
//    return Math.pow((col + 0.055) / 1.055, 2.4);
//  });
  $l = (0.2126 * c[0]) + (0.7152 * c[1]) + (0.0722 * c[2]);
  return ($l > 0.179) ? $darkColor : $lightColor;
}
function fromRGB($R, $G, $B)
{

    $R = dechex($R);
    if (strlen($R)<2)
    $R = '0'.$R;

    $G = dechex($G);
    if (strlen($G)<2)
    $G = '0'.$G;

    $B = dechex($B);
    if (strlen($B)<2)
    $B = '0'.$B;

    return '#' . $R . $G . $B;
}
$size = imagettfbbox(72, 0, "sourcecodeprosemibold.ttf", $imageInitial);
$x = round((90 - $size[2]) / 2) - 3;
$y = round((90 - $size[5]) / 2);
$img = imagecreate(90, 90);
$background = imagecolorallocate( $img, $r, $g, $b );
$colour = pickTextColorBasedOnBgColorSimple(fromRGB($r, $g, $b), "#FFFFFF", "#000000");
$percentRGB = [$r / 255, $g / 255, $b /255];
if(sqrt(0.299 * ($percentRGB[0] ** 2) + 0.587 * ($percentRGB[1] ** 2) + 0.114 * ($percentRGB[2] ** 2)) > 0.179){
    $text_colour = imagecolorallocate( $img, 0, 0, 0 );
} else {
    $text_colour = imagecolorallocate( $img, 255, 255, 255 );
}
imagettftext($img, 72, 0, $x, $y, $text_colour, "sourcecodeprosemibold.ttf", $imageInitial);
echo imagepng($img);
//echo pickTextColorBasedOnBgColorSimple(fromRGB($r, $g, $b), "#FFFFFF", "#000000");
