<?php

include 'connect.php';
$link = connect();

// Account

        $sql1 = "ALTER TABLE SamplingLocation
                MODIFY CorLatitude VARCHAR(10)";
        $sql2 = "ALTER TABLE SamplingLocation
        MODIFY CorLongitude VARCHAR(10)";

        $sql3 = "ALTER TABLE Gene ADD Housekeeping TINYINT";

        $sql5 = "ALTER TABLE Probe DROP COLUMN TargetSpecies";
        $sql6 = "ALTER TABLE Gene ADD TargetSpecies INT";
        $sql7 = "ALTER TABLE Gene ADD CONSTRAINT FOREIGN KEY (TargetSpecies) REFERENCES Species(SpeciesID)";
        $sql8 = "ALTER TABLE FishIndividual ADD EnteredByUser INT";
        $sql9 = "ALTER TABLE FishIndividual ADD CONSTRAINT FOREIGN KEY (EnteredByUser) REFERENCES Account(UserID)";
        $sql10 = "ALTER TABLE qPCRdata MODIFY WellPos VARCHAR(3)";
        $sql11 = "ALTER TABLE qPCRdata ADD CONSTRAINT FOREIGN KEY (qPCRID) REFERENCES qPCRrun(qPCRrunID)";
        $sql12 = "ALTER TABLE Probe ADD PrType VARCHAR(1)";
        $sql13 = "ALTER TABLE qPCRrun ADD qPCRKit VARCHAR(50)";
        $sql14 = "ALTER TABLE qPCRrun DROP COLUMN qPCRLab";
        $sql15 = "ALTER TABLE Temporary MODIFY Pwd VARCHAR(128)";
        $sql16 = "ALTER TABLE Account MODIFY Pwd VARCHAR(128)";

        $arr = array($sql5,$sql6,$sql7,$sql8,$sql9,$sql10,$sql11,$sql12,$sql13,$sql14,$sql15,$sql16);
        foreach ($arr as &$sql) {
            if (mysqli_query($link, $sql)){
                print "Table changed successfully";
            } else{
                print "ERROR: Unable to execute $sql. " . mysqli_error($link);
            }
        }
?>