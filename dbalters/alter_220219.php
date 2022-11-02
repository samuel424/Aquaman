<?php

include 'connect.php';
$link = connect();


        //$sql1 = SET PROBE NAME AS ALTERNATIVE PRIMARY KEY IN PROBE TABLE or unique
        $sql2 = "ALTER TABLE qPCRrun ADD Cycling INT";
        $sql3 = "ALTER TABLE qPCRrun ADD CONSTRAINT FOREIGN KEY (Cycling) REFERENCES Cycling(CyclingID)";
        $sql4 = "ALTER TABLE Probe ADD Fluor VARCHAR(10)";
        $sql5 = "ALTER TABLE qPCRdata DROP COLUMN CurveEval";
        

        $arr = array($sql2, $sql3, $sql4, $sql5);
        foreach ($arr as &$sql) {
            if (mysqli_query($link, $sql)){
                print "Table changed successfully";
            } else{
                print "ERROR: Unable to execute $sql. " . mysqli_error($link);
            }
        }
?>