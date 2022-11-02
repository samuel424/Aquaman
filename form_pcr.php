<?php
    include 'connect.php';
    include 'check_injection.php';

    function erase_qpcr_session(){
        foreach ($_SESSION as $key => $value){
            if ((strpos($key, 'qpcr_insert') === 0) or (strpos($key, 'geneid') === 0) ){
                unset($_SESSION[$key]);
            }
        }
        if (isset($_SESSION['genes_for_pcr'])){
            unset($_SESSION['genes_for_pcr']);
        }
        if (isset($_SESSION['species'])){
            unset($_SESSION['species']);
        }
        if (isset($_SESSION['genes'])){
            foreach ($_SESSION['genes'] as $value){
                if (isset($_SESSION[$value])){
                    unset($_SESSION[$value]);
                }
            }
            unset($_SESSION['genes']);
        } 
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $link = connect();
    if (!$resultgene = mysqli_query($link, "SELECT TargetGene, GName, SpeciesLatin, Housekeeping
    FROM Probe as p 
    LEFT JOIN 
    Gene AS g ON p.TargetGene = g.GeneID 
    LEFT JOIN 
    Species AS s ON g.TargetSpecies=s.SpeciesID 
    WHERE ((TargetGene IN
    (SELECT TargetGene FROM Probe WHERE PrType = 'F'))
    AND (TargetGene IN
    (SELECT TargetGene FROM Probe WHERE PrType = 'R'))
    AND (TargetGene IN
    (SELECT TargetGene FROM Probe WHERE PrType = 'P')))
    GROUP BY TargetGene ORDER BY TargetGene")){
        echo "Failed to query database for Gene options";
    }
    //Fetch probe data as [ProbeID, ProbeName, ProbeSequence, TargetGeneID, Forw/Rev/Probe]
    if (!$resultprobe = mysqli_query($link, "SELECT ProbeID, ProbeName, ProbeSequence, TargetGene, PrType FROM Probe ORDER BY TargetGene")){
        echo "Failed to query database for Probe options";
    }
    $probearray = [];
    while ($row = mysqli_fetch_row($resultprobe)){
        $probenames[$row[0]] = $row[1];
    }
    mysqli_data_seek($resultprobe, 0);

    if (!$resultcycling = mysqli_query($link, "SELECT * FROM Cycling")){
        echo "Failed to query database for Cycling options";
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
    include "standardAssets.php";
    pageheader();
    if (empty($_GET['success'])){
        include 'start_pcr.php';
    }
    ?>

            
<?php
if (!empty($_POST)){
    $g = 0;
    $p = 0;
    $s = 0;
    foreach($_POST as $key => $value){
        if (strpos($key, 'g_') === 0) {
            $g = $g + 1; //means genes have been selected
            }
        if (strpos($key, 'p_') === 0) {
            $p = $p + 1; //means primers have been selected
            }
        if (strpos($key, 's_') === 0) {
            $s = $s + 1; //means samples have been selected
            }
    }
    //genes have been selected
    if ($g > 0){
        $genes = [];
        $_SESSION['genes_for_pcr'] = [];
        //Put genes into genes array as GeneID => GeneID
        //populates gene array in order of presentation - i.e. in order of target gene
        foreach($_POST as $key => $value){
            if (strpos($key, 'g_') === 0) {
            $genes[$key] = $value;
            array_push($_SESSION['genes_for_pcr'], $key);
            }
        } 
        //Check that at least one housekeeping gene has been selected
        $i = 0;

        $genestring = '';
        foreach($genes as $key => $value){
            if(strpos($key, 'g_h_') === 0) {
                $i = $i + 1;
            }
            $genestring = $genestring.','.$value;
        }

        $genestring = '('.trim($genestring, ',').')';

        if ($i < 1){
            erase_qpcr_session();
            header('Location: form_pcr.php?error=house');
            exit();
        } 

        $species_query = "SELECT TargetSpecies FROM gene WHERE GeneID IN $genestring GROUP BY TargetSpecies";
        $species_check = mysqli_query($link, $species_query); 
        if(mysqli_num_rows($species_check) > 1){
            dconnect($link);
            erase_qpcr_session();
            header('Location: form_pcr.php?error=multiplespecies');
        }
        
        mysqli_data_seek($species_check, 0);

        while ($roww = mysqli_fetch_row($species_check)){
            $_SESSION['species123abc'] = $roww[0];
        }

        //populates genes in session in same order - by target gene
        $_SESSION['genes'] = [];
        foreach($genes as $key => $value){
            array_push($_SESSION['genes'], $value);
        }

        echo '<div class="body">';
        echo '<form action="form_pcr.php" method="POST">';
        echo '<label for="Probe"><b>Choose primers/probes:</b></label><br>';
        $gene_names = [];
        //Put gene names in array as GeneID => GeneName
        while ($row = mysqli_fetch_row($resultgene)){
            if (in_array($row[0], $genes)){
                $gene_names[$row[0]] = $row[1];
            }
        }
        echo "<table border='1'>"; 
        echo "<tr><th>Include</th><th>Probe name</th><th>Gene</th><th>Type</th></tr>";
        //Fetch probe data as [ProbeID, ProbeName, ProbeSequence, TargetGeneID, Forw/Rev/Probe]
        while($row = mysqli_fetch_row($resultprobe)){
            if (in_array($row[3], $genes)){ // = if TargetGene ID is in $genes array -> print
                echo '<tr><td>';
                if (preg_match("/{$row[4]}/i", "F")){
                    echo '<input type="checkbox" name="p_f_'.$row[0].'" value = "'.$row[3].'">';;
               } elseif (preg_match("/{$row[4]}/i", "R")){
                    echo '<input type="checkbox" name="p_r_'.$row[0].'" value = "'.$row[3].'">';
               } elseif (preg_match("/{$row[4]}/i", "P")){
                    echo '<input type="checkbox" name="p_pr_'.$row[0].'" value = "'.$row[3].'">';
               }
                echo '</td><td>';
                echo $row[1];
                echo '</td><td>';
                echo '<i> '.$gene_names[$row[3]].'</i>';
                echo '</td><td>';
                if (preg_match("/{$row[4]}/i", "F")){
                     echo '<b>Forward </b>';
                } elseif (preg_match("/{$row[4]}/i", "R")){
                    echo '<b>Reverse </b>';
                } elseif (preg_match("/{$row[4]}/i", "P")){
                    echo '<b>Probe </b>';
                }
                echo '</td></tr>';
            }
        }
        echo '</table><br>';
        echo '<input type="submit" value="Confirm primers"><br>';
    }

    //primers/probe have been selected
    if ($p > 0){
        //session_start();
        $p_f = []; //will contain geneIDs for which there are primers selected
        $p_r = [];
        $p_p = [];
        foreach ($_POST as $key => $value){
            //Check that every value has a only one key with p_f_, p_r_, p_p_
            if(strpos($key, 'p_f_') === 0){
                array_push($p_f, $value);
                $key = str_replace('p_f_', '', $key);
                $_POST[$key] = $value;
            } 
            elseif (strpos($key, 'p_r_') === 0){
                array_push($p_r, $value);
                $key = str_replace('p_r_', '', $key);
                $_POST[$key] = $value;
            }
            elseif(strpos($key, 'p_pr_') === 0){
                array_push($p_p, $value);
                $key = str_replace('p_pr_', '', $key);
                $_POST[$key] = $value;
            } 
        }

        //check that there is one exact set of forward-revese-probe for every gene
        if (($p_f != $p_r) or ($p_r != $p_p) or (count($_SESSION['genes']) != count($p_f))){
            erase_qpcr_session();
            dconnect($link);
            header('Location: form_pcr.php?error=primer');
            exit();
        } 

        //start arrays for every gene 
        foreach($p_f as $value){
            $gene_id = 'geneid'.strval($value);
            $_SESSION[$gene_id] = [];
        }

        //fill arrays for every gene with primer names
        foreach($_POST as $key => $value){
            if (strpos($key, 'p_') === FALSE){
                $gene_in_post = 'geneid'.strval($value);
                array_push($_SESSION[$gene_in_post], $key);
            }
        }

        echo "<form action='form_pcr.php' method='POST'>";
        echo '<b>Select sample RNA:</b><br>';

        $species_idsession = intval($_SESSION['species123abc']);

        if (!$resultjoin = mysqli_query($link,
        "SELECT a.RNAID, SpeciesLatin, SType, LocationName, STimestamp FROM
        (SELECT RNAID FROM rnasample AS r
        LEFT JOIN qpcrdata AS q ON r.RNAID=q.qPCRRNA 
        LEFT JOIN qpcrrun AS run ON q.qPCRID=run.qPCRrunID 
        WHERE 
        (r.RNAID NOT IN 
        (SELECT qPCRRNA FROM qpcrrun as qr 
        LEFT JOIN qpcrdata as q   
        ON qr.qPCRrunID = q.qPCRID 
        WHERE ((qPCRRNA IS NOT NULL) AND (qr.qPCRSuccess = 1))))
        GROUP BY r.RNAID) as a 
        INNER JOIN 
        (SELECT r.RNAID, SpeciesLatin, s.SType, sl.LocationName, fs.STimestamp 
        FROM rnasample AS r INNER JOIN ssample as s ON r.RSampleID = s.SsampleID 
        INNER JOIN fishindividual AS f ON s.SFishID = f.FishID 
        INNER JOIN species AS sp ON f.Species = sp.SpeciesID 
        INNER JOIN fieldsampling AS fs ON f.FishSamplingID = fs.SamplingID 
        INNER JOIN samplinglocation AS sl ON fs.LocationID = sl.LocationID WHERE SpeciesID = $species_idsession) 
        AS b ON a.RNAID = b.RNAID GROUP BY a.RNAID")){
        echo "Failed to query database for RSampleID options";
    }

        //echo limits for n_samples given replicates
        for ($i = 1;$i <= 3;$i++){
            $max = intval((96 - 2*count($p_f))/($i*count($p_f)));
            echo '<i>Max '.$max.' samples if run in '.$i.' replicate(s)<br></i>';
        }

        echo '<b>_______________________</b><br>';
        
        //display RNAs available



        while ($row = mysqli_fetch_row($resultjoin)){
            echo '<input type = "checkbox" name="s_'.$row[0].'" value="'.$row[0].'">';
            echo $row[0].' (<i>'.$row[1].'</i> | '.$row[2].' | ['.$row[3].' : '.$row[4].'])<br>'; 
        }

        echo '<b>_______________________</b><br>';

        echo '<label for="extra">Enter previously tested samples (RNA ID) (separated by ","): </label>';
        echo '<input type="text" name="s_extra"> <br>';

        echo '<label for="reps">Enter number of replicates: </label>';
        echo '<input type="number" name="reps" min="1" max="10" required><br>';
        echo '<input type ="submit" value="Confirm samples">';
        echo '</form>';
    }
    // samples have been selected
    if ($s > 0){
        echo '<div>';

        $samples = [];
        $i = 0;
        $n_genes = count($_SESSION['genes']);
        $reps = $_POST['reps'];
        $n_samples = 0;

        //collect samples in array, count the number of samples
        foreach($_POST as $key => $value){
            if ((strpos($key, 's_') === 0) and (strpos($key, 's_extra') !== 0)){
                array_push($samples, $value);
                $n_samples = $n_samples + 1;
            }
        }

        if (!empty($_POST['s_extra'])){
            $clean = check_injection($link, $_POST);
            $extra_samples = explode(',', $clean['s_extra']);
            foreach($extra_samples as $value){
                $value = intval($value);
                array_push($samples, $value);
                $n_samples = $n_samples + 1;
            }
        }
        
        // samples * genes * replicates + controls
        $wells_used = count($samples) * $reps * $n_genes + (2*$n_genes);

        if ($wells_used > 96){
            erase_qpcr_session();
            dconnect($link);
            header('Location: form_pcr.php?error=excesssamples');
            exit();
        }

        //Fetches gene name from geneID in $_SESSION genes array
        // pairs them with fetched probe rows where the probe names are in the $_SESSION array specific to that gene
        echo 'Genes to be tested:<br>';
        while ($row = mysqli_fetch_row($resultgene)){
            if (in_array($row[0], $_SESSION['genes'])){ //if geneid of row among genes in session
                echo '<i>'.$row[1].'</i>, (id = '.$row[0].')';  //echo gene name and id
                echo ' with primers/probe:<br>';
                $i = 0;
                while ($row_p = mysqli_fetch_row($resultprobe)){
                    if (in_array($row[0], $_SESSION['genes']) and $i < 3){      //if geneid in row from gene table in session genes
                        foreach($_SESSION['geneid'.$row[0]] as $value){         //for each primer/probe in session gene-specific array
                            echo $probenames[$value].', ';
                            $i = $i + 1;
                            if ($i == 3){               //once three primers/probe has been entered (=full set)
                                echo '<br>';
                                break 2;                //break 2 loops, go to next gene
                            }
                        }
                    }
                }
            }
        }

        //echo samples included
        echo 'RNA samples included:<br>';
        foreach($samples as $value){
                echo $value.'<br>';
            }
        echo 'Replicates: <br>';
        echo $reps;
        echo '</div><div>';

        //function that generates well names in the Xnn format
        function wellgen($A, $n){
            $Letter = strval($A);
            if ($n < 10){
                $Number = '0'.strval($n);
            } else {
            $Number = strval($n);
            }
            $Well = $Letter.$Number;
            return $Well;
        }        

        $rows = ['A','B','C','D','E','F','G','H'];
        $columns = range(1, 12);

        echo '<table border="1">';
        echo '<tr><th>Well</th><th>RNA</th><th>Gene ID</th><tr>';
        $w = 0; //well counter
        $s_count = 0; //sample per repetition counter
        $g_count = 0; //gene counter
        $sg_count = 0; //"samples per gene" counter
        $well_list = []; //list of well names used
        $sample_list = []; //list of samples in each well (1:1 well name ratio)
        $pc = 1;    //positive control on
        $nc = 0;    //negative control off (turns on after positive control has been entered)

        //makes a table row by row, : well name, sample, geneid (ordered by geneid)
        foreach($columns as $n_column){ //column outer loop (1-12)
            foreach($rows as $n_row){   //row inner loop (A-H)
                if ($w < $wells_used){  //if less rows than wells to be entered
                    $well_name = wellgen($n_row, $n_column); //generate next well name
                    array_push($well_list, $well_name); //add well to well list
                    echo '<tr><td><b>'; 
                    echo $well_name;    //print well name in cell
                    echo '</b></td>';
                    
                    foreach(array_slice($_SESSION['genes'], $g_count) as $geneid){ //for each gene, starting from the gene counter element 
                                                                                    //(0 for first gene, 1 for second..), use geneid in loop 
                                                                                    //same order as in $_SESSION['genes']
                    
                        while($sg_count < ($n_samples * $reps)){ //while the number of reactions per gene is below what is announced
                            if($pc == 1){                       //start with positive control
                                echo '<td>PC</td>';
                                echo '<td>';                
                                echo $geneid;                   //echo current gene id
                                echo '</td></tr>';
                                array_push($sample_list, 'PC'); //add to sample list
                                $nc = 1;                        //turn on NC
                                $pc = 0;                        //turn off PC
                                break;                          //break loop to skip NC/sample entering
                            } elseif ($nc == 1){                
                                echo '<td>NTC</td>';            //enter NC 
                                echo '<td>';
                                echo $geneid;
                                echo '</td></tr>';
                                array_push($sample_list, 'NTC');
                                $nc = 0;                        //turn off nc
                                break;                          //break loop to skip sample entering
                            }
                            foreach(array_slice($samples, $s_count) as $sample){ //for each sample, slicing off previously entered samples in the start
                                echo '<td>';
                                echo $sample;                   //echo sample name 
                                echo '</td>';
                                $s_count = $s_count + 1;        // advance sample counter
                                $sg_count = $sg_count + 1;      // advance sample per gene counter
                                array_push($sample_list, $sample);  //add sample to list
                                echo '<td>';
                                echo $geneid;                   //echo current gene id
                                echo '</td></tr>';
                                break;                          //break loop for counter checks (if gene or sample slice has to changed)
                            }
                            if ($s_count == $n_samples){        //if all samples have been entered, reset counter for next repetition
                                $s_count = 0;
                            }
                            if ($sg_count == $n_samples * $reps){   //if all samples for a gene have been entered:
                                $g_count = $g_count + 1;            //advance gene counter
                                $sg_count = 0;                      //reset gene per sample counter
                                $pc = 1;                            //turn PC back on
                                break;                              //exit loop (necessary?)
                            }
                            break;      //break loop for new gene/rep/sample (will not be changed unless counters changed)
                        }
                    break;              //break loop for new row
                    }
                    $w = $w + 1;        //advance well counter
                }

            }
        }
        echo '</table>';
        echo '</div>';


        $resultprobe2 = mysqli_query($link, "SELECT ProbeID, ProbeName, TargetGene, PrType FROM Probe 
        WHERE TargetGene IN (".implode(',', $_SESSION['genes']).") ORDER BY PrType"); //gives order: FORWARD, PROBE, REVERSE

        $primer_list = []; //list of primers where key = geneID, value = primer ID in order: FORWARD - PROBE - REVERSE 
        $empty_array = [];

        //add empty arrays to primer list and give them the key of the geneID in the query
        while ($row = $resultprobe2 -> fetch_row()){
            $primer_list[$row[2]] = $empty_array; 
        }

        //same query can't be cycled twice, for some reason
        unset($resultprobe2);
        $resultprobe2 = mysqli_query($link, "SELECT ProbeID, ProbeName, TargetGene, PrType FROM Probe 
        WHERE TargetGene IN (".implode(',', $_SESSION['genes']).") ORDER BY PrType"); //gives order: FORWARD, PROBE, REVERSE

        //populates geneid-keyed arrays with primers in the order above (F-P-R)
        while ($row = $resultprobe2 -> fetch_row()){
            if (in_array($row[0], $_SESSION['geneid'.$row[2]])){
                array_push($primer_list[$row[2]], $row[0]);
            }
        }  

        $j = 0;


        for($k = 0; $k < count($sample_list); $k++){
            if ($k > ((($j+1)*$n_samples*$reps)+(2*$j+1))){ //i dont even know how or why this works but it does, at least for 1 or 2 genes
                $j = $j+1;
            }
            $well = $well_list[$k];     //well id
            if($sample_list[$k] == 'PC'){
                $rnaid = 'NULL';        //no rna if control
                $sample_type = 2;       // 2 = positive control
            } elseif ($sample_list[$k] == 'NTC'){
                $rnaid = 'NULL';        
                $sample_type = 3;       // 3 = negative control
            } else {
                $rnaid = $sample_list[$k]; //RNA ID
                $sample_type = 1;       // 1 = unknown
            } 
            $gene = $_SESSION['genes'][$j]; //gene id to get primer/probe id's
            $primer_f = $primer_list[$gene][0];     //get primer id from primer lists
            $probe = $primer_list[$gene][1];
            $primer_r = $primer_list[$gene][2];
            $_SESSION['qpcr_insert'.$k] = $well.'#'.$rnaid.'#'.$primer_f.'#'.$primer_r.'#'.$probe.'#'.$sample_type;
            //insert into session as own variable with data for each row
        }
        

        echo '<div>';
        include 'end_pcr.php';
        echo '</div>';
    }
    if (isset($_POST['machine'])){ //means run data has been entered
        $clean = check_injection($link, $_POST);

        //Date
        if (empty($_POST['date'])) {
            erase_qpcr_session();
            dconnect($link);
            header('Location: form_pcr.php?error=invalid&item=date');
            exit;
        } 
        $date = $_POST['date'];
        $date_ = explode('-', $date);
        if (checkdate($date_[0], $date_[1], $date_[2])) {
            erase_qpcr_session();
            dconnect($link);
            header('Location: form_pcr.php?error=invalid&item=date');
            exit;
        }

        // Hour
        if (empty($clean['hour'])) {
            erase_qpcr_session();
            dconnect($link);
            header("Location: form_pcr.php?error=invalid&item=hour");
            exit;
        }
        $hour = $clean['hour'];
        if (intval($hour) >= 24) {
            erase_qpcr_session();
            dconnect($link);
            header("Location: form_pcr.php?error=outside&item=hour&max=23");
            exit;
        }
        //minute
        if (empty($clean['minute'])) {
            erase_qpcr_session();
            dconnect($link);
            header('Location: form_pcr.php?error=invalid&item=minute');
            exit;
        }
        $min = $clean['minute'];
        if (intval($min) >= 60) {
            erase_qpcr_session();
            dconnect($link);
            header('Location: form_pcr.php?error=outside&item=minute&max=59');
            exit;
        }
        $time = "$date $hour:$min:00 ";//YYYY-MM-DD hh:mm:ss
        $machine = intval($clean['machine']);
        $user = intval($_SESSION['UserID']);
        $kit = $clean['kit'];
        $cycling = intval($clean['cycling']);

        //prepare run ID (otherwise mysql can't find latest entered run)
        $sql_prepare = "INSERT INTO qPCRrun (qPCRTime) VALUES (NULL)";

        $prepare_run = mysqli_query($link, $sql_prepare);
        if (!$prepare_run){
            erase_qpcr_session();
            dconnect($link);
            header('Location: form_pcr.php?error=sql');
            exit;
        }

        //retrieve latest run entry
        $test = mysqli_query($link, "SELECT MAX(qPCRrunID) FROM qPCRrun ");
        $row = mysqli_fetch_row($test);
        $qpcrid = intval($row[0]);

        mysqli_begin_transaction($link);
        try {
            $sql_run = "UPDATE qPCRrun
            SET qPCRTime = '$time',
            qPCRUser = $user,
            qPCRMachine = '$machine',
            qPCRKit = '$kit',
            Cycling = $cycling
            WHERE qPCRrun.qPCRrunID = $qpcrid";
    
            $insert_run = mysqli_query($link, $sql_run); //update run entry with data

            mysqli_commit($link);
        } catch (mysqli_sql_exception $exception) {
            mysqli_rollback($link);
            throw $exception;
            echo "<br>Failed to update the run table!";
        }

        if ($insert_run){
            $qpcr_data = [];
            foreach ($_SESSION as $key => $value){
                if (strpos($key, 'qpcr_insert') === 0){
                    array_push($qpcr_data, $value);
                    unset($_SESSION[$key]);
                }
            }


            foreach($qpcr_data as $value){
                $entry_data = explode('#', $value); // 0 = $well; 1 = $rnaid; 2 = $primer_f; 3 = $primer_r; 4 = $probe; 5 = $sample_type;
                $well = $entry_data[0];
                $rnaid = $entry_data[1];
                $primer_f = intval($entry_data[2]);
                $primer_r = intval($entry_data[3]);
                $probe = intval($entry_data[4]);
                $sample_type = $entry_data[5];
                if (strpos($rnaid, 'NULL') !== 0){ //separate queries for controls and samples, controls get RNA IDs otherwise
                    $rnaid = intval($rnaid);
                    $sql_qpcr = "INSERT INTO qPCRdata (qPCRID, WellPos, qPCRRNA, PrimerF, PrimerR, Probe, SampleType)
                    VALUES ($qpcrid, '$well', $rnaid, $primer_f, $primer_r, $probe, $sample_type)";
                } else {
                    $rnaid = NULL;
                    $sql_qpcr = "INSERT INTO qPCRdata (qPCRID, WellPos, PrimerF, PrimerR, Probe, SampleType)
                    VALUES ($qpcrid, '$well', $primer_f, $primer_r, $probe, $sample_type)";
                }
                
                $insert_data = mysqli_query($link, $sql_qpcr);
                }
             if ($insert_data){
                
                //if succesfully inserted, write a file to feed to the machine
                
                //header stuff
                $welldata = fopen('_welldata.txt','w');
                $runid = 'Run ID: '.$qpcrid.' Genes: ';
                fwrite($welldata, $runid);
                foreach ($_SESSION['genes_for_pcr'] as $value){
                    $gene_entry = $value .' ';
                    fwrite($welldata, $gene_entry);
                }
                fwrite($welldata, PHP_EOL);
                $header = 'Well'.chr(9).'Fluor'.chr(9).'Target'.chr(9).'Content'.chr(9).'Sample'.chr(9).PHP_EOL;
                fwrite($welldata, $header);

                //fill each line with data
                //essentially same code as for filling but no point in writing file if filling fails
                foreach($qpcr_data as $value){
                    $entry_data = explode('#', $value);
                    $well = $entry_data[0];
                    $rnaid = $entry_data[1];
                    $probe = intval($entry_data[4]);
                    $get_gene_name = "SELECT GName, Fluor from Probe AS p LEFT JOIN Gene AS g ON p.TargetGene = g.GeneID WHERE ProbeID = $probe";
                    $row = mysqli_fetch_row(mysqli_query($link, $get_gene_name));
                    $target = $row[0];
                    $fluor = $row[1];
                    $sample_type = intval($entry_data[5]);
                    if ($sample_type == 1){
                        $content = 'Unkn';
                    } elseif($sample_type == 2){
                        $content = 'Pos Ctrl';
                    } elseif($sample_type == 3){
                        $content = 'Neg Ctrl';
                    }
                    $line = $well.chr(9).$fluor.chr(9).$target.chr(9).$content.chr(9).$rnaid.chr(9).PHP_EOL;
                    fwrite($welldata, $line);
                }
                fclose($welldata);
                unset($_SESSION['genes_for_pcr']);
                unset($_SESSION['genes']);
                dconnect($link);
                header('Location: form_pcr.php?success=qpcrrun');
                exit;
            } else {
                erase_qpcr_session();
                dconnect($link);
                header('Location: form_pcr.php?error=sql&help');
                exit;
            }
        } else {
            dconnect($link);
            erase_qpcr_session();
            header('Location: form_pcr.php?error=sql');
            exit;
        }
    }
}
?>
        
    </div> <!--/page-->
    <?php pagefooter();?>
</body>
