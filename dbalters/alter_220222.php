<?php
    include 'connect.php';
    $link = connect();

    $sql1 = "ALTER TABLE LabAffiliation ADD LabRole TINYINT  NOT NULL DEFAULT 0";

    $arr = array($sql1);
    foreach ($arr as &$sql) {
        if (mysqli_query($link, $sql)){
            print "Table changed successfully";
        } else{
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }
    }
?>