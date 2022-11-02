<?php
    include 'connect.php';
    $link = connect();

    $sql1 = "ALTER TABLE FishIndividual ADD EnteredByUser INT";
    $sql2 = "ALTER TABLE FishIndividual ADD FOREIGN KEY (EnteredByUser) REFERENCES Account(UserID);";

    $arr = array($sql1, $sql2);
    foreach ($arr as &$sql) {
        if (mysqli_query($link, $sql)){
            print "Table changed successfully";
        } else{
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }
    }
?>