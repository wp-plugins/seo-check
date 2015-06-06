<?php

function whitespaces_imagestring($image, $font, $x, $y, $string, $color) {
    $font_height = imagefontheight($font);
    $font_width = imagefontwidth($font);
    $image_height = imagesy($image);
    $image_width = imagesx($image);
    $max_characters = (int) ($image_width - $x) / $font_width;
    $next_offset_y = $y;

    for ($i = 0, $exploded_string = explode("\n", $string), $i_count = count($exploded_string); $i < $i_count; $i++) {
        $exploded_wrapped_string = explode("\n", wordwrap(str_replace("\t", "    ", $exploded_string[$i]), $max_characters, "\n"));
        $j_count = count($exploded_wrapped_string);
        for ($j = 0; $j < $j_count; $j++) {
            imagestring($image, $font, $x, $next_offset_y, $exploded_wrapped_string[$j], $color);
            $next_offset_y += $font_height;

            if ($next_offset_y >= $image_height - $y) {
                return;
            }
        }
    }
}

$arrayPhone = explode('|', $_GET["factorPhone"]);
$stringOUT = "";
foreach ($arrayPhone as $value) {
    $stringOUT .= $value . "\n";
}

header("Content-Type: image/png");
$im = @imagecreate(strlen($stringOUT)*4, strlen($stringOUT)- 27) or die("Cannot Initialize new GD image stream");
$background_color = imagecolorallocate($im, 247, 247, 247);
$text_color = imagecolorallocate($im, 51, 51, 51);
whitespaces_imagestring($im, 5, 5, 5, $stringOUT, $text_color);
imagepng($im);
imagedestroy($im);

