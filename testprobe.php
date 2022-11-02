<?php

include 'connect.php';
$link = connect();

if (!$resultprobe = mysqli_query($link, "SELECT * FROM Probe ORDER BY TargetGene")){
        echo "Failed to query database for Probe options";
    }

echo 'ROWS =';
    echo mysqli_num_rows($resultprobe);
    while ($row = $resultprobe -> fetch_row()){
        echo '<br>';
        echo '1';
        //echo $row['ProbeID'];
            //$pp_array[$row[0]] = $row[1];
    }

?>