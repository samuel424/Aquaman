<?php
    include 'connect.php';
    include "standardAssets.php";
    include 'check_injection.php';
    $link = connect();
    if(!$result_Freezer = mysqli_query($link,"SELECT fr.FreezerID, FModel, l.LabID, l.LabName FROM freezer AS fr 
    LEFT JOIN Laboratory AS l ON fr.FLabID = l.LabID")){
        echo "Failed loading freezer";
    }
    if(!$result_FishID = mysqli_query($link,"SELECT 
    f.FishID, s.SpeciesLatin , f.Sex, l.LocationName, fs.STimestamp, fr.FID  
    FROM fishindividual AS f 
    LEFT JOIN Species AS s ON f.Species = s.SpeciesID
    LEFT JOIN FieldSampling AS fs ON f.FishSamplingID = fs.SamplingID
    LEFT JOIN SamplingLocation AS l ON fs.LocationID = l.LocationID 
    LEFT JOIN (
        SELECT MAX(fr.FreezeID) AS FID, fr.FrFish 
        FROM Freeze AS fr 
        WHERE fr.FrOutTime IS NULL 
        GROUP BY fr.FrFish
    ) AS fr ON f.FishID = fr.FrFish 
    ")){ 
        echo "Failed loading fish";
    }
    if(!$result_Sample = mysqli_query($link,"SELECT 
    s.SsampleID, s.DissectTime, s.SType, f.FishID, sp.SpeciesLatin, fr.FID
    FROM ssample AS s
    LEFT JOIN fishindividual AS f ON s.SFishID = f.FishID
    LEFT JOIN Species AS sp ON f.Species = sp.SpeciesID
    LEFT JOIN (
        SELECT MAX(fr.FreezeID) AS FID, fr.FrSample 
        FROM Freeze AS fr 
        WHERE fr.FrOutTime IS NULL 
        GROUP BY fr.FrSample
    ) AS fr ON s.SsampleID = fr.FrSample
    ")){
        echo "Failed loading tissues";
    }
    if(!$result_RNA = mysqli_query($link,"SELECT 
    r.RNAID, f.FishID, sp.SpeciesLatin, s.SsampleID, s.SType, r.ExtractionTimestamp, fr.FID
    FROM rnasample AS r
    LEFT JOIN Ssample AS s ON r.RSampleID = s.SsampleID
    LEFT JOIN fishindividual AS f ON s.SFishID = f.FishID
    LEFT JOIN Species AS sp ON f.Species = sp.SpeciesID
    LEFT JOIN (
        SELECT MAX(fr.FreezeID) AS FID, fr.FrRNA 
        FROM Freeze AS fr 
        WHERE fr.FrOutTime IS NULL 
        GROUP BY fr.FrRNA
    ) AS fr ON r.RNAID = fr.FrRNA
    ")){
        echo "Failed loading RNA";
    }
    if(!$result_frozen = mysqli_query($link, "SELECT 
    fr.FreezeID, fr.Temperature, fr.FrInTime AS intime, fr.FrOutTime AS outtime, fr.FrType, frr.FreezerID, frr.FModel, 
    f.FishID , f.SpeciesLatin, f.Sex, f.LocationName, f.STimestamp AS caught, 
    s.SsampleID, s.SType, s.FishID AS Sf, s.SpeciesLatin AS Ssp, s.DissectTime, 
    r.RNAID, r.ExtractionTimestamp AS Rtime, r.SsampleID AS Rs, r.SType AS Rst, r.FishID AS Rf, r.SpeciesLatin AS Rsp 
    FROM freeze AS fr LEFT JOIN Freezer AS frr ON fr.FrFreezer = frr.FreezerID 
    LEFT JOIN (
        SELECT f.FishID, sp.SpeciesLatin, f.Sex, l.LocationName, fs.STimestamp 
        FROM fishindividual AS f 
        LEFT JOIN Species AS sp ON f.Species = sp.SpeciesID
        LEFT JOIN FieldSampling AS fs ON f.FishSamplingID = fs.SamplingID
        LEFT JOIN SamplingLocation AS l ON fs.LocationID = l.LocationID 
        ) AS f ON fr.FrFish = f.FishID
    LEFT JOIN (
        SELECT s.SsampleID, s.SType, f1.FishID, sp1.SpeciesLatin, s.DissectTime 
        FROM Ssample AS s 
        LEFT JOIN fishindividual AS f1 ON s.SFishID = f1.FishID 
        LEFT JOIN Species AS sp1 ON f1.Species = sp1.SpeciesID 
        ) AS s ON fr.FrSample = s.SsampleID
    LEFT JOIN ( 
        SELECT r.RNAID, r.ExtractionTimestamp, s2.SsampleID, s2.SType, f2.FishID, sp2.SpeciesLatin 
        FROM RNAsample AS r
        LEFT JOIN Ssample AS s2 ON r.RSampleID = s2.SsampleID 
        LEFT JOIN fishindividual AS f2 ON s2.SFishID = f2.FishID
        LEFT JOIN Species AS sp2 ON f2.Species = sp2.SpeciesID 
        ) AS r ON fr.FrRNA = r.RNAID"
    )) {
        echo "Failed load froozen".mysqli_error($link);
    }
    $s_res = FALSE;
    if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
        if (!empty($_POST["search"])) {
            $clean = check_injection($link, $_POST);
            $search = $clean["search"];
            $result = mysqli_query($link,"SELECT FreezeID FROM Freeze WHERE FreezeID = $search");
            if (mysqli_num_rows($result) > 0) {
                $s_res = TRUE;
            }
        }   
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Åquamän</title>
    <link rel="stylesheet" type="text/css" href="css/main.css?ts=<?=time()?>">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js?ts=<?=time()?>"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?ts=<?=time()?>"></script>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v5.15.4/js/all.js?ts=<?=time()?>" crossorigin="anonymous"></script>
        <!-- Google fonts-->
    
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Trirong">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Rancho&effect=fire-animation">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <link href='https://fonts.googleapis.com/css?family=Vampiro One' rel='stylesheet'>
        
<script src="scroll-down.js"></script>

<style>
h1 {
font-family: Trirong;


h2 {
font-family: Trirong;

}
h3 {
font-family: Trirong;

}

h3 {
font-family: "Trirong", serif;

}


</style>

    </head>
    <body style="font-family: Trirong";>
    <?php pageheader();?>
    <!--Content-->
    <div class="body">
        <div>
            <h2>Insert into freezer </h2>
            <form action="in_freezer.php" method="POST" style="border:1px solid #ccc">

                <label for='FreezerID'>FreezerID:</label>
                <select name='FreezerID' id='FreezerID'>
                    <?php
                    while($row = mysqli_fetch_assoc($result_Freezer)) { //, l.
                        $catid = $row["FreezerID"];
                        $catname = $row["FreezerID"].': '.$row["FModel"].' ('.$row["LabID"].': '.$row["LabName"].')';
                        echo "<option value='$catid'>$catname</option>"; 
                    }
                    ?> 
                </select> 
                <label for="Temp">Temperature:</label>
                <input type="number" placeholder="Temp" name="Temp" required><br>

                <p>Select at least one type of sample:</p>
                <label for='FrFishID'>FrFishID:</label>
                <select name='FrFishID' id='FrFishID'>
                    <option value="-1">id: species gender location catch_time</option>
                    <?php
                        while($row = mysqli_fetch_assoc($result_FishID)) {
                            if ($row["FID"] == NULL) {
                                $catid = $row['FishID'];
                                $catname = $row['FishID'].': '.$row['SpeciesLatin'].' '.$row['Sex'].' '.$row['LocationName'].' '.$row['STimestamp'].' '.$row['FID'].' '.$row['FrOutTime'];
                                echo "<option value='$catid'>$catname</option>"; 
                            }
                        }
                        ?>   
                </select>
                <br>
                 
                <label for='FrSample'>FrSample:</label>
                <select name='FrSample' id='FrSample'>
                    <option value="-1">id: disect_time tissue - species(fish_id)</option>
                    <?php
                        while($row = mysqli_fetch_assoc($result_Sample)) {
                            if ($row["FID"] == NULL) {
                                $catid = $row["SsampleID"];
                                $catname = $row['SsampleID'].': '.$row["DissectTime"].' '.$row["SType"].' - '.$row['SpeciesLatin'].'('.$row['FishID'].')';
                                echo "<option value='$catid'> $catname</option>"; 
                            }
                        }
                        ?>
                </select>
                <br>
                
                <label for='FrRNA'>FrRNA:</label>
                <select name='FrRNA' id='FrRNA'>
                    <option value="-1">id: extract_time tissue (tissue_id) - species (fish_id)</option>
                    <?php
                        while($row = mysqli_fetch_assoc($result_RNA)) {
                            if ($row["FID"] == NULL) {
                                $catid = $row['RNAID'];
                                $catname = $row['RNAID'].': '.$row['ExtractionTimestamp'].' '.$row['SType'].'('.$row['SsampleID'].') - '.$row['SpeciesLatin'].'('.$row['FishID'].')';
                                echo "<option value='$catid'>$catname</option>"; 
                            }
                        }
                        ?>
                </select>
                <br><br>
                
                <?php inputDate();?>

                <input type="submit" value="Insert">
            </form> <!--/in-->
        </div>

        <div>
            <h2>Take out from freezer </h2>
            <form action="out_freezer.php" method="post">
                <select name="freezeID" >
                    <option value="-1">-</option>
                    <?php
                    while ($row = mysqli_fetch_assoc($result_frozen)) {
                        if ($row['outtime'] == Null) {
                            echo "<option value='".$row['FreezeID']."'>" .$row['FreezeID'].": ";
                            echo  $row['intime'].' '."Freezer: " .$row['FreezerID'];
                            if ($row['FrType'] == 0) { //id: species gender location catch_time
                                echo "Fish: ".$row['FishID'].": ".$row['SpeciesLatin']." ".$row['Sex'].' '.$row['LocationName']." ".$row['caught'];
                            } elseif ($row['FrType'] == 1) { //id: disect_time tissue - species(fish_id)
                                echo "Tissue: ".$row['SsampleID'].': '.$row["DissectTime"].' '.$row['SType']." - ".$row['Ssp']."(".$row['Sf'].")";
                            } elseif ($row['FrType'] == 2) { //id: extract_time tissue (tissue_id) - species (fish_id)
                                echo "RNA: ".$row['RNAID'].": ".$row['Rtime'].' - '.$row['Rst']."(".$row['Rs'].") - ".$row['Rsp']." (".$row['Rf'].")";
                            }

                            echo "</option>";
                        }
                    }
                ?></select><br>
                <?php inputDate();?>
                <input type="submit" value="Take out">
            </form> <!--/out-->
        </div>
        
        <div> <!-- search-->
            <h>Search Freezer by ID:</h>
            <form action='form_freeze.php' method='POST'>
                <input type='text' name='search'>
                <input type='submit' value='Search'>
            </form>
        
            <table border='1'> 
                <tr>
                    <th>Freeze ID</th>
                    <th>FreezerID</th>
                    <th>Freezer Model</th>
                    <th>Temperature</th>
                    <th>Time In:</th>
                    <th>Time Out</th>
                    <th>FrType</th>
                    <th>Sample</th>
                </tr>
            
            <?php
                mysqli_data_seek($result_frozen, 0);
                while ($row = mysqli_fetch_assoc($result_frozen)) {
                    if (!$s_res || ($s_res && $row['FreezerID'] == $search)) {
                        if ($row['FrType'] == 0) {
                            $type = "Fish";
                            $sample = $row['FishID'].": ".$row['SpeciesLatin']." ".$row['Sex'].' '.$row['LocationName']." ".$row['caught'];
                        } elseif ($row['FrType'] == 1) {
                            $type = "Tissue";
                            $sample = $row['SsampleID'].': '.$row["DissectTime"].' '.$row['SType']." - ".$row['Ssp']."(".$row['Sf'].")";
                        } elseif ($row['FrType'] == 2) {
                            $type = "RNA";
                            $sample = $row['RNAID'].": ".$row['Rtime'].' - '.$row['Rst']."(".$row['Rs'].") - ".$row['Rsp']." (".$row['Rf'].")";
                        }
                        echo "<tr>
                            <td>".$row['FreezeID']."</td>
                            <td>".$row['FreezerID']."</td>
                            <td>".$row['FModel']."</td>
                            <td>".$row['Temperature']."</td>
                            <td>".$row['intime']."</td>
                            <td>".$row['outtime']."</td>
                            <td> $type </td>
                            <td> $sample </td>
                        </tr>";
                    }
                }
            ?>
            </table>
        </div><!-- /search-->
    </div> <!--/Content-->

    <?php pagefooter();?>
</body>
</html>