<?php

include 'connect.php';
$link = connect();

$sql1 = "SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));";

$arr = array($sql1);
foreach ($arr as &$sql) {
    if (mysqli_query($link, $sql)){
        print "Table changed successfully";
    } else{
        print "ERROR: Unable to execute $sql. " . mysqli_error($link);
    }
}