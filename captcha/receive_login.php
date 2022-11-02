<?php

//idk why we are starting a new session but the captcha isn't recognized otherwise
session_start();

$name = $_POST['Name'];

//checks that there is a name
if ($name == ""){
    die("Please enter a name");
}

//compares captcha to text
if ($_POST['captcha_challenge'] == $_SESSION['captcha_text']){
    echo "All good, your name is " . $name;
} else {
    echo "Wrong captcha";
}

?>