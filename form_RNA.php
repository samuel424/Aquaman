<?php
    include 'connect.php';
    include 'check_injection.php';

    $link = connect();
    if (!$resultsample = mysqli_query($link, "SELECT SsampleID, SType, SpeciesLatin, LocationName, STimestamp 
    FROM ssample AS s LEFT JOIN fishindividual AS f ON s.SFishID = f.FishID 
    LEFT JOIN species AS sp ON f.Species = sp.SpeciesID 
    LEFT JOIN fieldsampling AS fs ON f.FishSamplingID = fs.SamplingID 
    LEFT JOIN samplinglocation AS sl ON fs.LocationID = sl.LocationID 
    WHERE `SsampleID` NOT IN 
    (SELECT `RSampleID` FROM `RNASample` UNION 
    (SELECT `RSampleID` FROM `RNASample` 
    JOIN `qPCRdata` ON `RNASample`.RNAID = `qPCRdata`.qPCRRNA 
    JOIN `Probe` ON `qPCRdata`.Probe = `Probe`.ProbeID 
    JOIN `Gene` ON `Probe`.TargetGene = `Gene`.GeneID 
    WHERE `qPCRdata`.CT != 0 AND `Gene`.Housekeeping = 1))  
    ORDER BY `SsampleID` ASC")){
        print_r(mysqli_error($link));
        echo "Failed to query database for RSampleID options";
    }
    if (!$resultuser = mysqli_query($link, "SELECT * FROM `Account`")){
        echo "Failed to query database for UserID options";
    }
    if (!$resultlab = mysqli_query($link, "SELECT * FROM `Laboratory`")){
        echo "Failed to query database for LabID options";
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
    <?php include "standardAssets.php"; pageheader();?>
    <div class="body">
        <h1>RNA extraction</h1>
        <p> Please fill in this form below to add RNA extraction information:</p>
        <form action="add_RNA.php" method="POST">
            <label for="RsampleID"><b>Sample ID:</b></label><br>
            <!-- make a checkbox -->
            <?php
                $i = 0;
                while($row = mysqli_fetch_row($resultsample)){
                    $id = $row[0];
                    echo "<input type = \"checkbox\" name=\"checkbox[]\" value=$id>";
                    
                        echo $row[0].' ('.$row[1].' | <i>'.$row[2].'</i> | ['.$row[3]. ' : '.$row[4].'])';
                    ?>
                    <br>
                    <?php
                        $i++;
                    }
                    ?>
            <?php
                inputDate()
            ?>
            <label for="ExtractionUserID"><b>User:</b></label>
            <!-- drop down list -->
            <select name="ExtractionUserID" id="ExtractionUserID">
                <?php
                    // Echo User options for the drop down list
                    while($row = mysqli_fetch_assoc($resultuser)) {
                        $catid = $row["UserID"];
                        $catname = $row["Firstname"].' '.$row["Lastname"];
                        echo "<option value='$catid'>$catname</option>"; 
                    }
                ?>
            </select><br>
            <label for="ExtractionLabID"><b>Lab:</b></label>
            <!-- drop down list -->
            <select name='ExtractionLabID' id='ExtractionlabID'>
                <?php
                    // Echo lab options for the drop down list
                    while($row = mysqli_fetch_assoc($resultlab)) {
                        $catid = $row["LabID"];
                        $catname = $row["LabName"];
                        echo "<option value='$catid'>$catname</option>"; 
                    }
                ?>
            </select><br>
            <label for="ExtractionKit"><b>Kit:</b></label>
            <input type="text" name="ExtractionKit"><br>
            <label for="ExtractionKitLotnumber"><b>Kit LOT:</b></label>
            <input type="text" name="ExtractionKitLotnumber"><br>
            <label for="DNaseKit"><b>DNase Kit:</b></label>
            <input type="text" name="DNaseKit"><br>
            <label for="DNaseLot"><b>DNase LOT:</b></label>
            <input type="text" name="DNaseLot"><br>
            <input type="submit" value="Submit">
        </form>
    </div> 
    
    <div>
<h>Search RNA by ID:</h>
        <form action='form_RNA.php' method='POST'>
        <input type='text' name='search'/>
        <input type='submit' value='Search'/>
        <input type='submit' value='Show all RNA' name='all'>
    </form>
</div>
<?php


if(!empty($_POST["search"]) or $_POST['all'] == 'Show all RNA'){
    $clean = check_injection($link, $_POST);

    if ($_POST['all'] == 'Show all RNA'){
        $search_a = '1 = ';
        $search_r = '1 = ';
        $search = '1'; 
    } else {
        $search_a = 'r.RNAID = '; //for searching RNAs
        $search_r = 'qPCRRNA = '; //for searching PCR runs
        $search = intval($clean["search"]);
    }


        //Looks for samples from a fish or for all samples
        $result_pcr =  mysqli_query($link, "SELECT qPCRID, qPCRRNA FROM qpcrdata WHERE $search_r $search");
        if (mysqli_num_rows($result_pcr) > 0){
            //Associates PCR run IDs to an RNAID in an array
            $pcr_runs = [];
            while ($pcr_row = mysqli_fetch_assoc($result_pcr)){
                $rna_id = $pcr_row['qPCRRNA'];
                $pcr_id = $pcr_row['qPCRID'];
                //If no run ID for a sample - start an array
                if (!isset($pcr_runs[$rna_id])){
                    $pcr_runs[$rna_id] = [$pcr_id];
                //if run already in array, skip
                } elseif (in_array($pcr_id, $pcr_runs[$rna_id])) {
                    continue;
                //else push
                } else {
                    array_push($pcr_runs[$rna_id], $pcr_id);
                }
            }
        } else {
            $pcr_runs = "None";
        }
    
    
    //Connect to DB for search
    //Do the search
    
        $sql = "SELECT * FROM
        ( SELECT RNAID, RsampleID, SType, SFishID, LabName, FreezerID, FreezeID FROM ssample AS s 
        LEFT JOIN rnasample as r 
        ON s.SsampleID = r.RSampleID
        LEFT JOIN freeze as f 
        ON r.RNAID = f.FrRNA
        LEFT JOIN freezer as fr 
        ON f.FrFreezer = fr.FreezerID 
        LEFT JOIN laboratory as l 
        ON fr.FLabID = l.LabID) AS a LEFT JOIN
        (SELECT RNAID, LabName from laboratory AS l RIGHT JOIN rnasample AS r ON l.LabID = r.ExtractionLabID) as b ON a.RNAID = b.RNAID
        WHERE
        ((a.RNAID, FreezeID) IN 
        (SELECT FrRNA, MAX(FreezeID) FROM Freeze GROUP BY FrRNA) 
        OR (a.RNAID NOT IN (SELECT FrRNA FROM Freeze WHERE FrRNA IS NOT NULL)))
        AND $search_a $search ORDER BY a.RNAID";

        $result = mysqli_query($link,$sql);
        if (mysqli_num_rows($result) > 0){
        echo "<table border='1'>"; 
        echo "<tr><th>RNA ID</th><th>Sample ID</th><th>Tissue</th><th>Fish ID</th><th>Extraction Lab</th><th>Freezer Lab</th><th>Freezer ID</th><th>PCR runs</th></tr>";
        while($row = mysqli_fetch_row($result)){
            if (empty($row[5])){
                $freezeout = "(out)"; // print (out) if the sample is out of the freezer
            } else {
                $freezeout = "";
            }
    
            echo "<tr><td>";
            echo $row[0];
            echo "</td><td>";
            echo $row[1];
            echo "</td><td>";
            echo $row[2];
            echo "</td><td>";
            echo $row[3];
            echo "</td><td>";
            echo $row[8];
            echo "</td><td>";
            echo $row[4];
            echo "</td><td>";
            echo $row[5] . '<i>'.$freezeout.'</i>'; //Current (or last) freezer ID
            echo "</td><td>";
            //Loops through array with RNAs and prints every sample for a sample
            foreach ($pcr_runs[strval($row[0])] as $run_id){
                echo $run_id . ',';
            }
            echo "</td></tr>";
        }
            echo "</table>";
         } else { 
            dconnect($link);
            echo "none";
            header('Location: form_rna.php?error=noresults');
        }
   }

?>


    <!--/page-->
    <?php pagefooter();?>
</body>
