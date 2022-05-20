<?php

$imgpath = "duck.jpg";
$img = imagecreatefromjpeg($imgpath);
$size = getimagesize($imgpath);
$width = $size[0];
$height = $size[1];
$red   = imagecolorallocate($img, 255,   0,   0);

// Number of cells
$xgrid = 5;
$ygrid = 5;

// Calulate each cell width/height
$xgridsize = $width / $xgrid;
$hgridsize = $height / $ygrid;

// Remember col
$c = 'A';

// Y
for ($j=0; $j < $ygrid; $j++) {

    // X
    for ($i=0; $i < $xgrid; $i++) {

        // Dynamic x/y coords
        $sy = $hgridsize * $j;
        $sx = $xgridsize * $i;

        // Draw rectangle
        imagerectangle($img, $sx, $sy, $sx + $xgridsize, $sy + $hgridsize, $red);

        // Draw text
        addTextToCell($img, $sx, $xgridsize, $sy + $hgridsize, $hgridsize, $c . ($i + 1));
    }

    // Bumb cols
    $c++;
}

function addTextToCell($img, $cellX, $cellWidth, $cellY, $cellHeight, $text) {

    // Calculate text size
    $text_box = imagettfbbox(20, 0, 'OpenSans', $text);
    $text_width = $text_box[2]-$text_box[0];
    $text_height = $text_box[7]-$text_box[1];

    // Calculate x/y position
    $textx = $cellX + ($cellWidth / 2) - $text_width;
    $texty = $cellY - ($cellHeight / 2) - $text_height;

    // Set color and draw
    $color = imagecolorallocate($img, 0, 0, 255);
    imagettftext($img, 20, 0, $textx, $texty, $color, 'OpenSans', $text);
}

// Save output as file
imagejpeg($img, 'output.jpg');
imagedestroy($img);
shell_exec('feh output.jpg');
