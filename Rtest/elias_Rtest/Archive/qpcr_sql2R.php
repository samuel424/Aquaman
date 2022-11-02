<?php
    /* when it is ready, change the string data and sql query to reflect the qpcr data table. this php script will then select 
    the data from a qpcr run and write it into a txt file. this txt file can then be read by the r script and do the manipulation
    that needs to be done on the data.

    r-script will need a way of knowing what gene is housekeeping and which are stress
    r-script will also need to put in the population where samples (RNA) originate from, i.e species, location, catchmethod, etc... 

    should r-scripts output be sent back to the DB?

    if qPCR file data is stored in data tables in the DB, do we need to update the ERD so that attributes of the table
    reflect the features of qPCR data? (well, sample, ct,...)

    will the r-script need the origin of the samples as an additional feature? (this feature would be equivalent to normal v cancer cells)
    or should the sample feature be treated as the origin? if the later, how can i know how to group the samples so that i can treat the 
    groups equivalent to tumor v normal?
    */

    set_include_path('C:\wamp64\www\Aquaman'); // needed until move to main folder
    include "connect.php";
    $link = connect();

    $stringData = "LocationID   NDate   ReporterID  NoiseRank \n";

    $myFile = "theDF.txt";
    $fo = fopen($myFile, 'w') or die("can't open file");

    // read from db and write to txt file
    $sql = mysqli_query($link,"SELECT * FROM noise");
    while($data=mysqli_fetch_array($sql)) {
        $stringData.=$data['LocationID']."   ".$data['NDate']." ".$data['ReporterID']."  ".$data['NoiseRank']."\n";
    }
    fwrite($fo, $stringData);
    fclose($fo);

	///unlink for deleting txt file
    $df = 0;
    exec('A:/School/Programs/Code/language/R/R-4.1.1/bin/Rscript.exe qpcr_processData.r ' .$df);
    echo("<img src='graph.png' />");






?>