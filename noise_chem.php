<?php

$house = $_SESSION['house'];

if (!$resultexpedition = mysqli_query($link, "SELECT n.LocationID, LocationName, NoiseRank, MAX(NDate)
FROM noise as n 
LEFT JOIN samplinglocation as s ON n.LocationID = s.LocationID 
WHERE n.LocationID IN 
(SELECT fieldsampling.LocationID FROM fieldsampling WHERE SamplingID IN
(SELECT FishSamplingID FROM fishindividual WHERE FishID IN 
(SELECT SfishID FROM Ssample WHERE SsampleID IN 
(SELECT RsampleID from rnasample WHERE RNAID IN 
(SELECT qPCRRNA FROM qpcrdata 
WHERE Probe IN (SELECT ProbeID from probe WHERE TargetGene = 2)
GROUP BY qPCRRNA) 
GROUP BY RsampleID) 
GROUP BY SfishID)
GROUP BY fieldsampling.LocationID))
GROUP BY n.LocationID")){
    print_r(mysqli_error($link));
    echo "Failed to query database for expedition options";
}

echo '<b>Select locations (only showing locations with reported noise and reported qPCR data for housekeeping gene id '.$house.' from species id '.$_SESSION['species'].')</b><br>';
echo '<form action="form_chemanalysis.php" method="post">';
if(mysqli_num_rows($resultexpedition) > 0){
    echo '<table border="1">';
    echo '<tr><th>Include</th><th>Location</th><th>Date of latest report</th></tr>';
    while($row = mysqli_fetch_assoc($resultexpedition)){
        $id = $row['n.LocationID'];
        echo '<tr><td>';
        echo "<input type = 'checkbox' name=$id value=$id></td><td>";
        echo $row['LocationName'];
        echo '</td><td>';
        echo $row['MAX(NDate)'];
        echo '</td></tr>';
    }
    echo '</table>';
    echo '<input type="submit" value="Proceed" name = "Chemicals">';
}else{
    echo 'No data for any sampling available yet';
}
echo '</form>';
?>