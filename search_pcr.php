<?php
    include 'connect.php';
    include 'check_injection.php';
    $link = connect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>�quam�n</title>
    <link rel="stylesheet" href="css/main.css">
</head>

<body>
    <?php include "standardAssets.php"; pageheader();?>
    <div class="body">
        <h1>Search for qPCR runs</h1>

<h>Search PCR runs by ID:</h>
        <form action='search_pcr.php' method='POST'>
        <input type='text' name='search'/>
        <input type='submit' value='Search'/>
        <input type='submit' value='Show all runs' name='all'>
    </form>
</div>
<?php


if(!empty($_POST["search"]) or $_POST['all'] == 'Show all runs'){
    $clean = check_injection($link, $_POST);

    if ($_POST['all'] == 'Show all runs'){
        $search_a = '1 = ';
        $search_r = '1 = ';
        $search = '1'; 
    } else {
        $search_a = 'qPCRrunID = '; //for searching runs
        $search_r = 'q.qPCRID = '; //for searching genes
        $search = intval($clean["search"]);
    }


        //Looks for genes and species in every run
        $result_pcr =  mysqli_query($link, "SELECT qPCRID, SpeciesLatin, GName
        FROM qpcrdata as q 
        LEFT JOIN 
        probe as p ON q.PrimerF = p.ProbeID 
        LEFT JOIN gene AS g ON p.TargetGene = g.GeneID 
        LEFT JOIN species as s ON g.TargetSpecies = s.SpeciesID 
        WHERE $search_r $search");
        if (mysqli_num_rows($result_pcr) > 0){
            //Associates PCR run IDs to an RNAID in an array
            $species_list = [];
            $gene_list = [];
            while ($pcr_row = mysqli_fetch_assoc($result_pcr)){
                $pcr_id = $pcr_row['qPCRID'];
                $species = $pcr_row['SpeciesLatin'];
                $gene = $pcr_row['GName'];
                //If genes for runID - start an array
                if (!isset($gene_list[$pcr_id])){
                    $gene_list[$pcr_id] = [$gene];
                //if gene already in array, skip
                } elseif (in_array($gene, $gene_list[$pcr_id])) {
                    continue;
                //else push
                } else {
                    array_push($gene_list[$pcr_id], $gene);
                }
                $species_list[$pcr_id] = $species;
            }
        } else {
            $species_list = NULL;
            $gene_list = NULL;
        }
    
        $result_count = mysqli_query($link, "SELECT qPCRID, COUNT(qPCRID) FROM qpcrdata GROUP BY qPCRID");
        if (mysqli_num_rows($result_count) > 0){
            $counts = [];
            while ($row = mysqli_fetch_row($result_count)){
                $runid = $row[0];
                $samples = $row[1];
                $counts[$runid] = $samples;
            }
        }


    //Connect to DB for search
    //Do the search
    
        $sql = "SELECT qPCRrunID, qPCRTime, MachineModel, LabName, qPCRUser, qPCRSuccess 
        FROM qpcrrun as qr  
        LEFT JOIN qpcrmachine AS m ON qr.qPCRMachine = m.MachineID
        LEFT JOIN laboratory AS l ON m.MachineLab = l.LabID
        WHERE $search_a $search";

        $result = mysqli_query($link,$sql);
        if (mysqli_num_rows($result) > 0){
        echo "<table border='1'>"; 
        echo "<tr><th>qPCR run ID</th><th>Time</th><th>Machine</th><th>Lab</th><th>Run by (User ID)<th>Species</th><th>Genes</th><th>Total samples</th><th>Status</th></tr>";
        while($row = mysqli_fetch_row($result)){
            if ($row[5] == 1){
                $status = "Success";
            } elseif ($row[5] === '0') {
                $status = "Fail";
            } else {
                $status = "<i>In progress...</i>";
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
            echo $row[4];
            echo "</td><td>";
            echo "<i>".$species_list[$row[0]]."</i>";
            echo "</td><td>";
            foreach($gene_list[$row[0]] as $genename){
                echo $genename.', ';
            }
            echo "</td><td>";
            echo $counts[$row[0]];
            echo "</td><td>";
            echo $status; //complete/fail/in progress
            echo "</td></tr>";
        }
            echo "</table>";
         } else { 
            dconnect($link);
            echo "none";
            header('Location: search_pcr.php?error=noresults');
        }
   }

?>


    <!--/page-->
    <?php pagefooter();?>
</body>
