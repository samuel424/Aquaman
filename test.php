<?php

include 'connect.php';
$link = connect();

session_start();






//print_r($_SESSION);
$user = $_SESSION['UserID'];
$userdir = 'head_'.$user;
$resultdir = $userdir.'/test_'.$user;
mkdir($userdir);
mkdir($resultdir);

//echo __DIR__;



$testfile = fopen($resultdir."/test.txt", "w");

fwrite($testfile, "this is a test");

fclose($testfile);

$highfile = fopen($userdir."/high.txt", "w");

fwrite($highfile, "this is a test");

fclose($highfile);


$allfiles = glob($userdir.'/*');

print_r($allfiles);

$resultfiles = glob($resultdir.'/*');
foreach ($resultfiles as $file){
    unlink($file);
}

$allfiles = glob($userdir.'/*');

print_r($allfiles);

foreach ($allfiles as $file){
    unlink($file);
}

$allfiles = glob($userdir.'/*');

print_r($allfiles);

?>