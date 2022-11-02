<?php

$house = $_SESSION['house'];
$noise = $_SESSION['noise'];

if ($noise){
    $noisecondition = "AND f.LocationID in (SELECT LocationID FROM noise)";
    $noisestring = " from locations with reported noise data";
    $noiseerror = "noise data and ";
} else {
    $noisecondition = "";
    $noisestring = "";
    $noiseerror = "";
}

if (!$resultexpedition = mysqli_query($link, "SELECT LocationName, STimestamp, SamplingID 
FROM fieldsampling as f 
LEFT JOIN samplinglocation as s ON f.LocationID = s.LocationID 
WHERE f.SamplingID IN 
(SELECT FishSamplingID FROM fishindividual WHERE FishID IN 
(SELECT SfishID FROM Ssample WHERE SsampleID IN 
(SELECT RsampleID from rnasample WHERE RNAID IN 
(SELECT qPCRRNA FROM qpcrdata 
WHERE Probe IN (SELECT ProbeID from probe WHERE TargetGene = $house)
GROUP BY qPCRRNA) 
GROUP BY RsampleID) 
GROUP BY SfishID)) $noisecondition")){
    print_r(mysqli_error($link));
    echo "Failed to query database for expedition options";
}

echo '<b>Select expeditions (only showing expeditions with reported qPCR data for housekeeping gene id '.$house;
echo ' from species id '.$_SESSION['species'].$noisestring.')</b><br>';
echo '<form action="form_chemanalysis.php" method="post">';
if(mysqli_num_rows($resultexpedition) > 0){
    echo '<table border="1">';
    echo '<tr><th>Include</th><th>Location</th><th>Time</th></tr>';
    while($row = mysqli_fetch_assoc($resultexpedition)){
        $id = $row['SamplingID'];
        echo '<tr><td>';
        echo "<input type = 'checkbox' name=$id value=$id></td><td>";
        echo $row['LocationName'];
        echo '</td><td>';
        echo $row['STimestamp'];
        echo '</td></tr>';
    }
    echo '</table>';
    if ($noise){
        echo '<input type="submit" value="Submit for analysis" name = "Expeditions">';
    } else {
        echo '<input type="submit" value="Continue" name = "Expeditions">';
    }
}else{
    echo 'No sufficient data for any sampling available yet (requires '.$noiseerror.'qPCR data for the selected species and housekeeping gene from any expedition';
}
echo '</form>';
?>