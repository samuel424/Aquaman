<?php

    include "connect.php";
    include "check_injection.php";
    include "standardAssets.php";
    $link = connect();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') { //coming from register form


        $clean = check_injection($link,$_POST);
        $user = $_SESSION["UserID"];
        $userdir = "mapdata".$user;
        clearfolders($userdir, $create = TRUE);

        //Species
        if (empty($clean['speciesID'])) {
            dconnect($link);
            header('Location: form_map.php?error=invalid&item=speciesID');
            exit;
        } 
        $selectedspecies = $clean['speciesID'];



         //Gene
        if (empty($clean['GeneID'])) {
            dconnect($link);
            header('Location: form_map.php?error=invalid&item=GeneID');
            exit;
        } 
        $selectedgene = $clean['GeneID'];
        $houseID = $_POST['HouseID'];

        if ($resultgene = mysqli_query($link,"SELECT GName FROM gene WHERE GeneID = $houseID")){
        } else {
            header('Location: form_map.php?error=sqlgene');
        }

        while($gene = mysqli_fetch_row($resultgene)){
            $house_name = $gene[0];
        }

        $sqlcoor = "SELECT samplinglocation.LocationID, `CorLatitude`, CorLongitude FROM `SamplingLocation`
        JOIN `FieldSampling` ON `SamplingLocation`.LocationID = `FieldSampling`.LocationID
        JOIN `FishIndividual` ON `FieldSampling`.SamplingID = `FishIndividual`.FishSamplingID
        JOIN `Species` ON `FishIndividual`.Species = `Species`.SpeciesID
        JOIN `Gene` ON `Species`.SpeciesID = `Gene`.TargetSpecies
        WHERE `Gene`.TargetSpecies = $selectedspecies
        AND `Gene`.GeneID = $selectedgene
        GROUP BY samplinglocation.LocationID";

        // Create arrays where all the coordinates can be stored 
        $All_longitudes = array();
        $All_latitudes = array();
        $All_locations =  array();

        if ($resultcoor = mysqli_query($link,$sqlcoor)){
        } else {
            echo "You have chosen a location where your species has
            not been sampled from or the respective gene has not been analyzed from such a species from that region. 
            Please press the reset button and try again with a different selection!";
        }

        // append to array
        while($row = mysqli_fetch_assoc($resultcoor)){
            array_push($All_locations,$row['LocationID']);
            array_push($All_latitudes,$row['CorLatitude']);
            array_push($All_longitudes,$row['CorLongitude']);
        }
        

        // new file with all the coordinates for a specific gene and species
        //$filecoordinates = 'C:/MAMP/htdocs/Aquaman/coordinates.csv';
        //$filecoordinates = 'D:/wamp64/www/projectfish/Aquaman/Aquaman/coordinates.csv';
        $filecoordinates = $userdir.'/coordinates.csv';
        // Array with the coordinates
        $arraycor = array(implode(',',$All_longitudes), implode(',',$All_latitudes));
        // add coordinates to file
        file_put_contents($filecoordinates,implode(PHP_EOL,$arraycor));
        // fancy map
        

        $qpcr_data = fopen($userdir.'/qpcr_process.txt', 'w');
        fwrite($qpcr_data, $house_name.PHP_EOL);
        fwrite($qpcr_data, $All_locations[0].PHP_EOL);
        $header = 'Well Sample Gene Population Ct Run'.PHP_EOL;
        fwrite($qpcr_data, $header);
        
        foreach ($All_locations as $location){

            $sql_pcr = "SELECT WellPos, qPCRRNA, GName, CT, qPCRID 
            FROM qpcrdata AS q 
            LEFT JOIN probe AS p 
            ON q.PrimerF = p.ProbeID 
            LEFT JOIN gene AS g 
            ON p.TargetGene = g.GeneID 
            WHERE qPCRRNA IN 
            (SELECT RNAID from rnasample 
            WHERE RSampleID IN 
            (SELECT SsampleID FROM ssample 
            WHERE SFishID IN 
            (SELECT FishID FROM fishindividual 
            WHERE species = $selectedspecies AND FishSamplingID IN
            (SELECT SamplingID FROM fieldsampling 
            WHERE LocationID = $location)) 
            GROUP BY SsampleID) 
            GROUP BY RNAID) AND
            qPCRID IN 
            (SELECT qPCRID FROM qpcrdata WHERE Probe IN 
            (SELECT ProbeID FROM probe AS p WHERE p.TargetGene = $houseID) GROUP BY qPCRID)
            AND CT IS NOT NULL AND (GeneID = $selectedgene OR GeneID = $houseID)";
                if (!$resultpcr = mysqli_query($link, $sql_pcr)){
                    echo "You requested qPCR data that has not been generated yet. Please press the reset button
                    and try again with a different selection!";
                }
                else{
                    if (!$pcrrow = mysqli_fetch_row($resultpcr)){
                        header('Location: form_map.php?error=invalid&item=qPCR data');
                        exit;
                    }else{
                        $datarow = $pcrrow[0].' '.$pcrrow[1].' '.$pcrrow[2].' '.$location.' '.$pcrrow[3].' '.$pcrrow[4].PHP_EOL;
                        fwrite($qpcr_data, $datarow);
                        while($pcrrow = mysqli_fetch_row($resultpcr)){
                            $datarow = $pcrrow[0].' '.$pcrrow[1].' '.$pcrrow[2].' '.$location.' '.$pcrrow[3].' '.$pcrrow[4].PHP_EOL;
                            fwrite($qpcr_data, $datarow);
                        }
                    }
                }
        }
        fclose($qpcr_data);
        if (!$resultpcr || !$resultcoor){
            echo "Your request is invalid! Please check that your housekeeping gene and gene are connected to your species
            and that your genes and species are connected to a sampling location. Also, please check
            that you have qPCR data for your selection.";
            echo "<br>";
            echo "Please press the reset button and try again with a different selection!";
        }else{
            $analysis = 1;
            //exec('C:/MAMP/bin/R-4.0.3/bin/rscript.exe qpcr_FGE2map.R '.$user .' '.$analysis);
            //exec('D:/wamp64/bin/R-4.1.1/bin/rscript.exe qpcr_FGE2map.R '.$user .' '.$analysis);
            exec('C:/MAMP/bin/R-3.6.2/bin/rscript.exe qpcr_FGE2map.R '.$user .' '.$analysis);
            // provides output file qpcr_map_FGE.txt


            //analysis = 1 -> mapdata
            //analysis = 2 -> regdata

            //exec("C:/MAMP/bin/R-4.0.3/bin/rscript.exe fancymap.R ". $user);
            //exec("\"D:/wamp64/bin/R-4.1.1/bin/rscript.exe\" fancymap.R $user");
            exec("\"C:\Program Files\R\R-3.6.2\bin\\x64\Rscript.exe\" fancymap.R $user");
            exec("C:/MAMP/bin/R-3.6.2/bin/rscript.exe fancymap.R". $user);
            // return image tag
            echo "<img src=".$userdir."/fancymap.png>"; 
        
             // open regression (~'sample') data file
            $firstline = TRUE;
            $opentable = fopen($userdir.'/results/map_sites_table.txt', 'r');
            echo '<h2>Data summary:</h2>';
            echo '<table border="1">';
            while (!feof($opentable)){
                $oneline = fgets($opentable);
                // uses first line as header for table
                if($firstline){
                    $header = explode(',', $oneline);
                    echo '<tr>';
                    foreach ($header as $hvalue){
                        echo '<th>'.$hvalue.'</th>';
                    }
                    echo '</tr>';
                    $firstline = FALSE;
                } else {
                    // fills table with data values used for regression (+ tests lines to ignore last one, which is just a space)
                    $test=trim($oneline);
                    if (!empty($test)){
                        $row = explode(',', $oneline);
                        echo '<tr>';
                        foreach ($row as $rvalue){
                            echo '<td>'.$rvalue.'</td>';
                        }
                        echo '</tr>';
                    }
            }
            }
            echo '</table>';
        }




        dconnect($link);
    }



    else {
       dconnect($link);
       header('Location: form_map.php');
       exit;
    }
echo "<form action='form_map.php' method='post'><input type='submit' name='reset' value='Reset'>";
echo "<label for='reset'>Reset map tool</label></form>";







?>