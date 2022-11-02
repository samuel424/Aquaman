<?php
    
    include 'connect.php';
    $link = connect();

    $sql = "ALTER TABLE Probe MODIFY ProbeName VARCHAR(30)";
    if (mysqli_query($link, $sql)){
        print "Table changed successfully";
    } else{
        print "ERROR: Unable to execute $sql. " . mysqli_error($link);
    }
?>