<?php
header('Content-type:image/png');
$imageInitial = $_GET["imageInitial"];
$r = $_GET['r'];
$g = $_GET['g'];
$b = $_GET['b'];
function pickTextColorBasedOnBgColorAdvanced($bgColor, $lightColor, $darkColor) {
  $color = (substr($bgColor,0, 1) === '#') ? substr($bgColor, 1) : $bgColor;
  $r = hexdec(substr($color, 0, 2)); // hexToR
  $g = hexdec(substr($color, 2, 2)); // hexToG
  $b = hexdec(substr($color, 4, 2));
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
  return ($l > 0.179) ? darkColor : lightColor;
}
$size = imagettfbbox(72, 0, "sourcecodeprosemibold.ttf", $imageInitial);
$x = round((90 - $size[2]) / 2) - 3;
$y = round((90 - $size[5]) / 2);
$img = imagecreate(90, 90);
$background = imagecolorallocate( $img, $r, $g, $b );
$colour = pickTextColorBasedOnBgColorAdvanced($background, "#FFFFFF", "#000000");
if($colour == "#000000"){
    $text_colour = imagecolorallocate( $img, 0, 0, 0 );
} else {
    $text_colour = imagecolorallocate( $img, 255, 255, 255 );
}
imagettftext($img, 72, 0, $x, $y, $text_colour, "sourcecodeprosemibold.ttf", $imageInitial);
echo imagepng($img);
