<?php
    include 'connect.php';
    $link = connect();

    
    
    $sql = "ALTER TABLE Probe
    ADD COLUMN PrType VARCHAR(1);";

    if (mysqli_query($link, $sql)){
        print "Table changed successfully";
    } else{
        print "ERROR: Unable to execute $sql. " . mysqli_error($link);
    }