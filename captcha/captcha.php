<?php
//you need to have the gd module installed for this to work
//for MAMP, add "extension=gd" somewhere in php.ini (for your used php version) to install
//for WAMP, [___fill in blank___]

//captcha code modified from https://code.tutsplus.com/tutorials/build-your-own-captcha-and-contact-form-in-php--net-5362


//start a session
session_start();

//only capital latin letters for simplicity
$permitted_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

//Function to make a random string of $strength characters
function generate_string($input, $strength = 5) {
    $input_length = strlen($input);
    $random_string = '';
    for($i = 0; $i < $strength; $i++) {
            $random_character = $input[rand(0, $input_length - 1)];
            $random_string .= $random_character;
    }
     
    return $random_string;
}

//makes an image
$image = imagecreatetruecolor(200, 50);

//random hues of colours
$red = rand(125, 175);
$green = rand(125, 175);
$blue = rand(125, 175);

//mix random hues for random colours
for($i = 0; $i < 5; $i++) {
  $colors[] = imagecolorallocate($image, $red - 20*$i, $green - 20*$i, $blue - 20*$i);
}

//first random colour is the background
imagefill($image, 0, 0, $colors[0]);

//add random rectangles to the image with other colours from the random colour array
for($i = 0; $i < 10; $i++) {
    imagesetthickness($image, rand(2, 10));
    $rect_color = $colors[rand(1, 4)];
    imagerectangle($image, rand(-10, 190), rand(-10, 10), rand(-10, 190), rand(40, 60), $rect_color);
  }  

//define text colours
$black = imagecolorallocate($image, 0, 0, 0);
$white = imagecolorallocate($image, 255, 255, 255);
$textcolors = [$black, $white];
 
//define fonts (these must be in the same folder, else change path)
$fonts = ['arial.ttf','cour.ttf'];

//generate string
$string_length = 6;
$captcha_string = generate_string($permitted_chars, $string_length);

//save captcha text in session
$_SESSION['captcha_text'] = $captcha_string;

//add letters to the image with random colours, fonts, angle, and y-axis position
for($i = 0; $i < $string_length; $i++) {
    $letter_space = 200/$string_length;
    $initial = 15;
    
    imagettftext($image, 20, rand(-15, 15), $initial + $i*$letter_space, rand(20, 40), $textcolors[rand(0, 1)], $fonts[array_rand($fonts)], $captcha_string[$i]);
  }

//tell the browser that this is an image and display it
header('Content-type: image/png');
imagepng($image);
imagedestroy($image); //idk why destroy but whatever, as long as it works

?>