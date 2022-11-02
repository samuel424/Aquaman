<?php
    include "connect.php";
    include "standardAssets.php"; 
    $link = connect();
    if(!$result_species = mysqli_query($link,"SELECT * FROM species WHERE SpeciesID IN 
    (SELECT Species FROM fishindividual WHERE FishID IN 
    (SELECT SFishID FROM Ssample WHERE SsampleID IN 
    (SELECT RsampleID from rnasample WHERE RNAID IN 
    (SELECT qPCRRNA FROM qpcrdata GROUP BY qPCRRNA) 
    GROUP BY RsampleID) GROUP BY SFishID) GROUP BY Species)")){
        echo "Failed to query database for species options";
    }


// IGNORING TISSUES ATM
// TO ADD TISSUES, ADD TISSUE SELECTION PART OF FORM AFTER CHOOSING CHEMICALS
// ADD 'WHERE' CLAUSE FOR TISSUE UNDER SSAMPLE IN $sql_pcr

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
    ?>
    <!--Content-->
    <div class="body">
        <h1>Correlate physicochemical data to stress</h1><br>
        
<?php

// reset button action - deletes all data, i.e. files and session variables
if (isset($_POST['reset'])){
    $variables = ['species', 'house', 'sampling_ids','chem', 'housename', 'speciesname', 'do_analysis', 'noise', 'do_noise', 'redo_noise'];
    foreach ($variables as $var){
        if (isset($_SESSION[$var])){
            unset($_SESSION[$var]);
        }
    }


    $user = $_SESSION['UserID'];
    $userdir = 'regdata'.$user;
    clearfolders($userdir, $create = FALSE, $keepdata = FALSE, $removeresultdir = TRUE, $removedir = TRUE);
    header('Location: form_chemanalysis.php');
}

// if log is set, data has already been gathered and needs only to be logarithmized
if (!isset($_GET['log'])){
    if (empty($_POST)){

        // start_chem asks for species selection
        include 'start_chem.php';

    } elseif(isset($_POST['Species'])){

        // add species ID to session
        $_SESSION['species'] = intval($_POST['speciesID']);
        $species = $_SESSION['species'];

        // house_chem asks for housekeeping gene
        include 'house_chem.php';

    } elseif (isset($_POST['House'])){

        // add housekeeping gene ID to session
        $_SESSION['house'] = intval($_POST['HouseID']);

        include 'nchem_chem.php';

    } elseif (isset($_POST['nchem'])){

        if($_POST['nchem']=='chemicals'){
            $_SESSION['noise'] = FALSE;
        } else {
            $_SESSION['noise'] = TRUE;
        }
        // exp_chem asks to select expeditions (to get chemical data)
        // or to get what fish should be included in the noise analysis (+ closest noise date)

        include 'exp_chem.php';

    } elseif (isset($_POST['Expeditions'])){

        // remove 'expeditions' variable to only keep sampling IDs
        unset($_POST['Expeditions']);
        $sampling_ids = [];

        // add sampling IDs to array
        foreach ($_POST as $value){
            $one_id = intval($value);
            array_push($sampling_ids, $one_id);
        }

        // add array with sampling IDs to session
        $_SESSION['sampling_ids'] = $sampling_ids;

        if($_SESSION['noise']){
            unset($_SESSION['noise']);
            $_SESSION['do_noise'] = TRUE;
        } else {
            // mid_chem asks to select a chemical variable for correlation
            include 'mid_chem.php';
        }
    } 
}
    if ((isset($_POST['Chemicals'])) or (isset($_SESSION['do_noise']))){
        
        $_SESSION['do_analysis'] = TRUE;
        
        // unset do_noise but save value for later use but to prevent entry into this loop
        if (isset($_SESSION['do_noise'])){
            $noise = $_SESSION['do_noise'];
            $_SESSION['redo_noise'] = $noise;
            unset($_SESSION['do_noise']);
        }
        

        $user = $_SESSION['UserID'];
        $userdir = 'regdata'.$user;

        clearfolders($userdir, $create = TRUE, $keepdata = FALSE, $removeresultdir = FALSE, $removedir = FALSE);

        if ($noise){
            $chem = 'Noise';
            $noise_extraheader = chr(9).'NoiseDate'.chr(9).'SamplingDate'.chr(9).'DaysDiff';
        } else {
            // add chemical variable name to session
            $_SESSION['chem'] = $_POST['chem'];
            $chem = $_SESSION['chem'];
            $noise_extraheader = '';
        }

        // get necessary variables from session
        $sampling_ids = ($_SESSION['sampling_ids']);    
        $species = $_SESSION['species'];
        $houseID = $_SESSION['house'];

        // get housekeeping gene name for ct analysis file (and add to session [repeat use needed for efficient logarithmizing])
        $house_result = mysqli_query($link, "SELECT GName FROM gene WHERE GeneID = $houseID");
        $row = mysqli_fetch_row($house_result);
        $_SESSION['housename'] = $row[0];
        $house_name = $_SESSION['housename'];

        // open and add header to chemical data file
        $chem_data = fopen($userdir.'/_chem_data.txt', 'w');
        $header = 'Population'.chr(9).$chem.'_level'.$noise_extraheader.PHP_EOL;
        fwrite($chem_data, $header);

        // open and add header to pcr data file
        $qpcr_data = fopen($userdir.'/qpcr_process.txt', 'w');
        fwrite($qpcr_data, $house_name.PHP_EOL);
        fwrite($qpcr_data, $sampling_ids[0].PHP_EOL);
        $header = 'Well Sample Gene Population Ct Run'.PHP_EOL;
        fwrite($qpcr_data, $header);

        // for each sampling, query database for pcr data and chemical data, write to corresponding file
        foreach($sampling_ids as $sampling){

            if ($noise){
                //will select the noise rank closest in time to the sampling
                $sql_chem = "SELECT NoiseRank, Ndate, STimestamp, DATEDIFF(Ndate, STimestamp) AS days
                FROM noise AS n INNER JOIN fieldsampling AS f ON n.LocationID = f.LocationID
                WHERE f.SamplingID = $sampling
                ORDER BY days ASC LIMIT 1";
            } else {
                //chemical data query
                $sql_chem = "SELECT $chem FROM fieldsampling WHERE SamplingID = $sampling";
            }

            $resultchem = mysqli_query($link, $sql_chem);
            $chemrow = mysqli_fetch_row($resultchem);

            if($noise){
                $noise_extrarow = chr(9).$chemrow[1].chr(9).$chemrow[2].chr(9).$chemrow[3];
            } else{
                $noise_extrarow = '';
            }

            $chemregdatarow = $sampling.chr(9).$chemrow[0].$noise_extrarow.PHP_EOL;
            fwrite($chem_data, $chemregdatarow);

            // pcr data query
            // finds all data for specified species, samplingID and only includes runs with specified housekeeping gene
            $sql_pcr = "SELECT WellPos, qPCRRNA, Gname, CT, qPCRID 
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
            WHERE FishSamplingID = $sampling AND species = $species) 
            GROUP BY SsampleID) 
            GROUP BY RNAID) AND
            qPCRID IN 
            (SELECT qPCRID FROM qpcrdata WHERE Probe IN 
            (SELECT ProbeID FROM probe AS p WHERE p.TargetGene = $houseID) GROUP BY qPCRID)
            AND CT IS NOT NULL";
            $resultpcr = mysqli_query($link, $sql_pcr);
            while($pcrrow = mysqli_fetch_row($resultpcr)){
                $datarow = $pcrrow[0].' '.$pcrrow[1].' '.$pcrrow[2].' '.$sampling.' '.$pcrrow[3].' '.$pcrrow[4].PHP_EOL;
                fwrite($qpcr_data, $datarow);
            }
        }
        // close both files
        fclose($qpcr_data);
        fclose($chem_data);

        // add species name to session for later use
        $species = $_SESSION['species'];
        $spec_result = mysqli_query($link, "SELECT SpeciesLatin FROM species WHERE SpeciesID = $species");
        $row = mysqli_fetch_row($spec_result);
        $_SESSION['speciesname'] = $row[0];
        dconnect($link);
    }

    // new if-clause in case collected data has to be re-used for logarithmization
    if ((isset($_SESSION['do_analysis']) and $_SESSION['do_analysis'] == TRUE) or isset($_GET['log'])){

        // prevent re-entry into if statement
        $_SESSION['do_analysis'] = FALSE;

        // starts by deleting any previous files in results folder
        $user = $_SESSION['UserID'];
        $userdir = 'regdata'.$user;
        $resultdir = $userdir.'/results';
        //clearfolders($userdir, $create = FALSE, $keepdata = TRUE, $removeresultdir = FALSE, $removedir = FALSE);

        // executes ct analysis R file
        $analysis = 2; // 2 = linear regression
        exec('C:/MAMP/bin/R-4.0.3/bin/rscript.exe qpcr_FGE2map.R '.$user .' '.$analysis);
        
        
        // if user wants to logarithmize (or else ->...)
        if (isset($_GET['log'])){
            if ($_GET['log'] == 'true'){

            // notify data is logarithmic
            $log = '<b>(logarithmized (base = 10) data) </b>';

            // undo logarithmization
            $logoption = 'or (<a href="form_chemanalysis.php?log=false">undo</a>) logarithmizing data';

            // define log argument for graph script
            $logargument = 1;
            }
        } 
        if(!isset($_GET['log']) or $_GET['log'] == 'false') {
            // no logarithmization
            $log = '';

            // give option to use logarithms
            $logoption = ' or <a href="form_chemanalysis.php?log=true">logarithmizing data</a>';

            // define log argument for graph script
            $logargument = 0;
        }
        
        if (isset($_SESSION['redo_noise'])){
            $noise = $_SESSION['redo_noise'];
            $noisearg = 1;
            $chem = 'noise';
        } else {
            $noisearg = 0;
            $chem = $_SESSION['chem'];
        }

        // performs correlation analysis and prints graphs
        exec('C:/MAMP/bin/R-4.0.3/bin/rscript.exe corr_plots.R '.$logargument .' '.$noisearg.' '.$user);
    

        // retrieve session variables for display

        $spec_name = $_SESSION['speciesname'];
        $house_name = $_SESSION['housename'];
        $sampling_string = '';
        foreach ($_SESSION['sampling_ids'] as $value){
            $sampling_string = $sampling_string.','.$value;
        }

        // descriptive text
        // hyperlink from sampling_string redirects to search for samplings in form_rawmaterial
        echo 'Showing correlation of fold gene expression (FGE) '.$log.'of stress genes to '.$chem.' levels';
        echo '<br>Including all tested genes for <i>'.$spec_name.'</i> as compared to the housekeeping gene '.$house_name.'<br>';
        echo 'Populations (<a href= form_rawmaterial.php?link='.$sampling_string.'>sampling IDs</a>) included: '.$sampling_string;

        echo '<br><b>NOTE!</b> This is preliminary data and should be properly analyzed (e.g. with regard to sample size'.$logoption.')';
        echo '<br><br>';
        echo "<form action='form_chemanalysis.php' method='post'><input type='submit' name='reset' value='Reset'>";
        echo "<label for='reset'>Reset all variables and start anew</label></form>";

        // open regression analysis data file
        $openfile = fopen($resultdir.'/lm_coeff.txt', "r");

        // skip first line (changes to FALSE once the first line is passed)
        $firstline = TRUE;
        while (!feof($openfile)){
            $oneline = fgets($openfile);
            if(!$firstline){
                // trims line to catch last line (which is just a space character) and ignore it
                $test=trim($oneline);

                if (!empty($test)){
                    // 'explodes' array on spaces
                    $linearray = preg_split('/\s+/', $oneline);
                    
                    // graph title and picture
                    echo '<h2> Graph '.$linearray[0].'</h2>';
                    echo "<img src='".$linearray[5]."' />";

                    // table presenting regression data
                    echo '<table style ="border:1px solid black;border-collapse:collapse;">';
                    echo '<tr><td><b>Intercept</td><td>'.$linearray[1].'</td></tr>';
                    echo '<tr><td><b>Slope</td><td>'.$linearray[2].'</td></tr>';
                    echo '<tr><td><b>R-squared</td><td>'.$linearray[3].'</td></tr>';
                    echo '<tr><td><b>p-value</td><td>'.$linearray[4].'</td></tr>';
                    echo '</table>';
                }
            } else {
                $firstline = FALSE;
        }
        }

        // open regression (~'sample') data file
        $firstline = TRUE;
        $opentable = fopen($resultdir.'/graph_data.txt', 'r');
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
        ?>

    </div> <!--/page-->
    <?php pagefooter();?>
</body>
</html>