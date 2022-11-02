<?php
include 'connect.php';
$link = connect(TRUE); //connect to local host
if (!$link) {
    echo "Error: Unable to connect to MySQL." . mysqli_connect_error() . PHP_EOL; exit;
}

$val = mysqli_query($link, "SHOW DATABASES LIKE 'AQUAMAN'");
    if(!$val){
        if ($link->connect_error) {
            die("Connection failed: " .$link->connect_error);
        } else {
            $sql = "CREATE DATABASE AQUAMAN";
            if (mysqli_query($link, $sql)){
                echo "Database created successfully";
            } else {
                echo "ERROR: Not able to execute $sql. " .mysqli_error($link);
            }
        }
    }

include "populate.php";

dconnect($link); //disconnect
?>