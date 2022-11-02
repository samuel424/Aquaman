<?php
    include 'connect.php';
    include "standardAssets.php"; 
    include 'check_injection.php';
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
    //If not loged in header('Location: index.php')
    pageheader();
    $link = connect();
    if(!$result_species = mysqli_query($link,"SELECT * FROM Species")){
        echo "Failed to query database for species options";
    }
    if(!$result_catchmethod = mysqli_query($link,"SELECT * FROM Catchmethod")){
        echo "Failed to query database for catch method options";
    }
    if(!$result_expeditions = mysqli_query($link,"SELECT * FROM FieldSampling AS f
     JOIN SamplingLocation AS s ON
     f.LocationID = s.LocationID")){
        echo "Failed to query database for catch method options";
    }
?>
    <!--Content-->
<div class='body'>

 <form action="add_fish.php" method="POST" style="border:1px solid #ccc">
    <h2>Add fish </h2>
    <label for='samplingID'>Expedition:</label>
    <select name = 'fieldID' id = 'fieldID'>
        <?php
            // Echo expedition options for the drop down list
            while($row = mysqli_fetch_assoc($result_expeditions)) {
                $catid = $row["SamplingID"];
                $catname = $row["SamplingID"] . ': ' . $row['LocationName'] . ' ' . substr($row['STimestamp'],0,10);
                echo "<option value='$catid'>$catname</option>"; 
            }
        ?>
        </select><br>
    <label for='species'>Species:</label>
    <select name='speciesID' id='speciesID'>
        <!--Species-->
        <?php
            // Echo species options for the drop down list
            while($row = mysqli_fetch_assoc($result_species)) {
                $catid = $row["SpeciesID"];
                $catname = $row["SpeciesLatin"];
                echo "<option value='$catid'>$catname</option>"; 
            }
            ?>
        </select><br>

        <!--Catch method-->
        <label for = 'catchmethod'>Catch method: </label>
        <select name='catchmethodID' id='catchmethodID'>
        <?php
            // Echo user options for the drop down list
            while($row = mysqli_fetch_assoc($result_catchmethod)) {
                $catid = $row["CatchmethodID"];
                $catname = $row["CatchmethodName"];
                echo "<option value='$catid'>$catname</option>"; 
            }
        ?>
    </select><br>

    <label for="s_type">Sex:</label><br>
    <input type="checkbox" name="ch_male" value='A'>
    <label for="ch_male"> Male</label>
    <label for='male_number' style="font-size:14px">Number of specimens: </label>
    <input type="number" name="n_male" min = '1' max = '100'><br>

    <input type="checkbox" name="ch_female" value='B'>
    <label for="ch_female"> Female</label>
    <label for='female_number' style="font-size:14px">Number of specimens: </label>
    <input type="number" name="n_female" min = '1' max = '100'><br>
    <input type='submit' value = 'Add fish'/>

</form>

<!-- search-->
<div> <br>
<h>Search Fish by ID:</h>
    <form action='form_fish.php' method='POST'>
        <input type='text' name='search'/>
        <input type='submit' value='Search'/>
        <input type='submit' value='Show all fish' name='all'>
    </form>
</div>
<?php

//If entry in search field, initiate search for WHERE FishID = search_term
//If showing all fish, WHERE 1 = 1
if(!empty($_POST["search"]) or $_POST['all'] == 'Show all fish'){
    $clean = check_injection($link, $_POST);

    if ($_POST['all'] == 'Show all fish'){
        $search_a = '1 = ';
        $search = '1'; 
    } else {
        $search_a = 'a.FishID = '; //for searching in megatable where FishID is ambiguous
        $search_s = 'SFishID = '; //for searching in samples where FishID is SFishID (and FishID cannot be used)
        $search = intval($clean["search"]);
    }

    
    //Looks for samples from a fish or for all samples
    $result_samples =  mysqli_query($link, "SELECT SsampleID, SFishID FROM SSample WHERE $search_s $search");
    if (mysqli_num_rows($result_samples) > 0){
        //Associates sample IDs to a FishID in an array
        $fish_samples = [];
        while ($sample_row = mysqli_fetch_assoc($result_samples)){
            $fish_id = $sample_row['SFishID'];
            //If no FishID for a sample - start an array, otherwise push into existing
            if (!isset($fish_samples[$fish_id])){
                $fish_samples[$fish_id] = [$sample_row['SsampleID']];
            } else {
                array_push($fish_samples[$fish_id], $sample_row['SsampleID']);
        }
    }
    } else {
        $fish_samples = "None";
    }

    //HUUUUUGE SQL query for the table
    $result_main = mysqli_query($link, "SELECT * FROM 
    (SELECT FishID, Sex, CatchmethodName, LabName, FrFreezer, FrInTime, FreezeID
    FROM catchmethod AS cm
    LEFT JOIN fishindividual AS f
    ON cm.CatchmethodID = f.Catchmethod
    LEFT JOIN freeze AS fr
    ON f.FishID = fr.FrFish
    LEFT JOIN freezer AS frr
    ON fr.FrFreezer = frr.FreezerID
    LEFT JOIN laboratory AS lab
    ON frr.FLabID = lab.LabID)
    as a 
    LEFT JOIN 
    (SELECT FishID, LocationName, STimestamp 
    FROM FishIndividual as f 
    LEFT JOIN FieldSampling as fs 
    ON f.FishSamplingID = fs.SamplingID 
    LEFT JOIN SamplingLocation AS sl 
    ON fs.LocationID = sl.LocationID) 
    as b 
    ON a.FishID = b.FishID 
    LEFT JOIN 
    (SELECT FishID, SpeciesLatin FROM FishIndividual as f
    LEFT JOIN Species as s 
    ON f.Species = s.SpeciesID) 
    as c 
    ON b.FishID = c.FishID WHERE
    (((a.FishID, FreezeID) IN (SELECT FrFish, MAX(FreezeID) FROM Freeze GROUP BY FrFish)) 
    OR (a.FishID NOT IN (SELECT FrFish FROM Freeze WHERE FrFish IS NOT NULL))) AND $search_a $search");

    if ($search == 0 or mysqli_num_rows($result_main) < 1){ //searching for 0 doesn't do anything, for some reason
        dconnect($link);
        header('Location: form_fish.php?error=noresults');
    } else {
        echo "<table border='1'>";
        echo "<tr><th>Fish ID</th><th>Sex</th><th>Species</th><th>Catch Method</th><th>Catch location</th><th>Catch time</th><th>Freezer location</th>
        <th>Freezer ID</th><th>Samples taken?</th></tr>";
        while ($row = mysqli_fetch_row($result_main)){;
        if (empty($row[5])){
            $freezeout = "(out)"; // print (out) if the fish is out of the freezer
        } else {
            $freezeout = "";
        }

        echo "<tr><td>";
        echo $row[0];   //Fish ID
        echo "</td><td>";
        echo $row[1];   // Sex
        echo "</td><td>";
        echo "<i>".$row[11]."</i>"; //Latin name
        echo "</td><td>";
        echo $row[2];   //Catch Method
        echo "</td><td>";
        echo $row[8];   //Catch location
        echo "</td><td>";
        echo $row[9];   //Catch time
        echo "</td><td>";
        echo $row[3];   //Current (or last) freezer lab
        echo "</td><td>";
        echo $row[4].'<i> '.$freezeout.'</i>'; //Current (or last) freezer ID
        echo "</td><td>";
        //Loops through array with samples and prints every sample for a fish
        foreach ($fish_samples[strval($row[0])] as $sample_id){
            echo $sample_id . ',';
        }
        echo "</td></tr>";
     }  
     echo "</table>"; 
    }       
        
} 

//this is a useless comment

?>
 </form>
</div>


    <?php pagefooter();?>
</body>
</html> 