<?php
    include 'connect.php';
    include "standardAssets.php";
    include 'check_injection.php';
    $link = connect();
    if (!empty($_POST)){
    if(!empty($_POST["search"]) or $_POST['all'] == 'Show all samples'){
        $clean = check_injection($link, $_POST);
    
        if ($_POST['all'] == 'Show all samples'){
            $search_a = '1 = ';
            $search_r = '1 = ';
            $search = '1'; 
        } else {
            $search_a = 'a.SsampleID = '; //for searching samples
            $search_r = 'RsampleID = '; //for searching RNAs
            $search = intval($clean["search"]);
        }
    
        
        //Looks for samples from a fish or for all samples
        $result_rna =  mysqli_query($link, "SELECT RNAID, RSampleID FROM rnasample WHERE $search_r $search");
        if (mysqli_num_rows($result_rna) > 0){
            //Associates RNA IDs to a Sample ID in an array
            $rna_samples = [];
            while ($rna_row = mysqli_fetch_assoc($result_rna)){
                $rna_id = $rna_row['RNAID'];
                //If no FishID for a sample - start an array, otherwise push into existing
                if (!isset($rna_samples[$rna_id])){
                    $rna_samples[$rna_id] = [$rna_row['RNAID']];
                } else {
                    array_push($rna_samples[$rna_id], $rna_row['RNAID']);
            }
        }
        } else {
            $fish_samples = "None";
        }
    
        $sql = "SELECT * FROM 
        (SELECT SsampleID, SFishID, DissectTime, DissectUser, Stype, LabName, FreezerID, FreezeID FROM Ssample as s
         LEFT JOIN freeze as f ON s.SsampleID = f.FrSample 
         LEFT JOIN freezer as fr ON f.FrFreezer = fr.FreezerID 
         LEFT JOIN laboratory as l ON fr.FLabID = l.LabID) as a 
        LEFT JOIN 
        (SELECT FishID, SpeciesLatin FROM fishindividual as f 
        LEFT JOIN species AS s ON f.Species = s.SpeciesID) as b 
        ON a.SFishID = b.FishID WHERE
        (((a.SsampleID, FreezeID) IN (SELECT FrSample, MAX(FreezeID) FROM Freeze GROUP BY FrSample)) 
        OR (a.SsampleID NOT IN (SELECT FrSample FROM Freeze WHERE FrSample IS NOT NULL))) AND $search_a $search";
        $result = mysqli_query($link,$sql);
    
        if (mysqli_num_rows($result) == 0){
            dconnect($link);
            echo "none";
            header('Location: form_sample.php?error=noresults');
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
    <?php
    pageheader();
    
    if(!$result_user = mysqli_query($link,"SELECT * FROM `Account`")){
        echo "Failed to query database for UserID options";
    }
?>
    <!--Content-->
    <div class="body">
        <!-- the actual form-->
        <form action="add_sample.php" method="POST" style="border:1px solid #ccc">
            <h2>Add sample </h2>
               <label for="s_fishID">Fish ID:</label>
               <input type="number" name="s_fishID"><br>
               <?php
                   inputDate(TRUE);
               ?>
               <label for="s_type">Type of Dissection:</label><br>
               <input type="checkbox" name="ch_liver" value="liver">
               <label for="ch_liver"> Liver</label><br>
               <input type="checkbox" name="ch_eye" value="eye">
               <label for="ch_eye"> Eye</label><br>
               <input type="checkbox" name="ch_gills" value="gills">
               <label for="ch_gills"> Gills</label><br>
               <input type="checkbox" name="ch_other" value="1">
               <label for="ch_other"> Other: </label>
               <input type="text" name="text_other"><br>
               <input type="submit" value="Add sample(s)">
               <br>
        </form>
        
        <!-- search-->
        <div> <br>
        <h>Search Sample by ID:</h>
                <form action='form_sample.php' method='POST'>
                <input type='text' name='search'/>
                <input type='submit' value='Search'/>
                <input type='submit' value='Show all samples' name='all'>
            </form>
        </div>

    <?php
    if (!empty($_POST)){
        if (mysqli_num_rows($result) > 0){
        echo "<table border='1'>"; 
        echo "<tr><th>Sample ID</th><th>Fish ID</th><th>Species</th><th>Dissection time</th><th>Dissected by (user ID)</th><th>Tissue type</th><th>Freezer Lab</th><th>Freezer ID</th><th>RNA ID</th></tr>";
            while($row = mysqli_fetch_row($result)){
                if (empty($row[6])){
                    $freezeout = "(out)"; // print (out) if the sample is out of the freezer
                } else {
                    $freezeout = "";
                }
                echo "<tr><td>";
                echo $row[0];
                echo "</td><td>";
                echo $row[1];
                echo "</td><td>";
                echo $row[9];
                echo "</td><td>";
                echo $row[2];
                echo "</td><td>";
                echo $row[3];
                echo "</td><td>";
                echo $row[4];
                echo "</td><td>";
                echo $row[5];
                echo "</td><td>";
                echo $row[6] . '<i>'.$freezeout.'</i>'; //Current (or last) freezer ID
                echo "</td><td>";
                foreach ($rna_samples[strval($row[0])] as $sample_id){
                    echo $sample_id . ',';
                }
                echo "</td></tr>";
            }
            echo "</table>";
        }
    }
    ?>
    </div> <!--Page-->
    <?php 
        dconnect($link);
        pagefooter();
    ?>
</body>
</html>