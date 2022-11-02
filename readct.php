<?php
    include 'connect.php';
    include 'check_injection.php';
    include 'standardAssets.php';
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $link = connect(); //connect to local host
    mysqli_set_charset($link, 'utf8');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $clean = check_injection($link,$_POST);
        $ourfile = $clean['textfile'];
        // qPCRrunID
        if (empty($clean['qPCRrunID'])) {
            dconnect($link);
            header('Location: qPCR.php?error=invalid&item=qPCRrunID');
            exit;
        }
        $qPCRrunID = $clean["qPCRrunID"]; 
        // Responsible
        if (empty($clean['CurveAnalys'])) { // replace with login sheck
            dconnect($link);
            header('Location: login.php?error=login');
            exit;
        }
        $responsible = intval($_SESSION['UserID']); // replace with logged in

        //CurveEval
        $CurveEval = intval($clean["CurveEval"]);

        //Success or not
        if (empty($clean['successornot'])) { 
            dconnect($link);
            header('Location: qPCR.php?error=invalid&item=successornot');
            exit;
        }
        $successorfail = $clean["successornot"]; 


        $openfile = fopen($ourfile, "r");

        // Create an array with all the well positions 
        $Allwells = array();

        mysqli_autocommit($link, FALSE);
        $firstline = TRUE;

        //checks if end of file has been reached
        try {
        while (!feof($openfile)){
            $oneline = fgets($openfile); //reads one line
                if (!$firstline){
                $ourarray = explode(",", $oneline); //separates the line based on comma
                $Well = $ourarray[0];
                $Content = $ourarray[3];
                $Cq = $ourarray[6];
                //list($Well,$Fluor,$Target,$Content,$SampleID,$BiologicalSetName,$Cq) = $ourarray;
                // Add well position to array 
                array_push($Allwells,$Well);
                // Update entry where there is a negative control
                if ($Content == 'Neg Ctrl'){
                    $Cq = 0.00;
                    $stmt_negctrl = mysqli_prepare($link, 'UPDATE `qpcrdata` SET `Ct` = ?, `CurveAnalyst` = ? WHERE `qpcrdata`.WellPos = ? AND `qpcrdata`.qPCRID = ?');
                    mysqli_stmt_bind_param($stmt_negctrl, 'disi', $Cq, $responsible, $Well, $qPCRrunID);
                    $execute = mysqli_stmt_execute($stmt_negctrl);
                }
                    
                // Update table
                else{
                    $stmt_update = mysqli_prepare($link, 'UPDATE `qpcrdata` SET `Ct` = ?, `CurveAnalyst` = ? WHERE `qpcrdata`.WellPos = ? AND `qpcrdata`.qPCRID = ?');
                    mysqli_stmt_bind_param($stmt_update, 'disi', $Cq, $responsible, $Well, $qPCRrunID);
                    $execute = mysqli_stmt_execute($stmt_update);
                }
            } else {
                $firstline = FALSE;
            }
    }


        
        $badcurves = explode(",", $CurveEval);
        // Delete entries from table where the curves were bad
        foreach ($badcurves as $curve){
            $stmt_badcurve = mysqli_prepare($link, 'DELETE FROM `qpcrdata` WHERE `qpcrdata`.WellPos = ? AND `qpcrdata`.qPCRID = ?');
            mysqli_stmt_bind_param($stmt_badcurve, 'si', $curve, $qPCRrunID);
            $execute = mysqli_stmt_execute($stmt_badcurve);
        }
        
        $stmt_updaterun = mysqli_prepare($link, "UPDATE `qPCRrun` SET `qPCRSuccess` = ? WHERE `qPCRrun`.qPCRrunID = ?");

        if ($successorfail == 'yes'){
            $success = 1;
            mysqli_stmt_bind_param($stmt_updaterun,'ii', $success, $qPCRrunID);
            $execute = mysqli_stmt_execute($stmt_updaterun);

        }else{
            $success = 0;
            mysqli_stmt_bind_param($stmt_updaterun,'ii', $success, $qPCRrunID);
            $execute = mysqli_stmt_execute($stmt_updaterun);

            foreach ($Allwells as $separatewell){

                $stmt_deleteallentries = mysqli_prepare($link, "DELETE FROM `qpcrdata` WHERE `qpcrdata`.WellPos = ? AND `qpcrdata`.qPCRID = ?");
                mysqli_stmt_bind_param($stmt_deleteallentries, 'si', $separatewell, $qPCRrunID);
                $execute = mysqli_stmt_execute($stmt_deleteallentries);
            }
        }
    
        fclose($openfile);

        mysqli_autocommit($link, TRUE);
        header('Location: qPCR.php?success=retrievedata');
        echo "Successfully updated the table!";

    } catch (mysqli_sql_exception $exception) {
        mysqli_rollback($link);
        throw $exception;
        echo "<br>Failed to update the table!";
    }
    
    dconnect($link); //dissconnect

} else {
        dconnect($link);
        header('Location: qPCR.php');
        exit;
    }
//     // if ($execute){
//     //     header('Location: qPCR.php?success=retrievedata');
//     //     echo "Successfully updated the table!";
//     // }   else {
//     //     header('Location: qPCR.php?error=sqlupdate');
//     //     echo "Failed to update the table!";
//     // }
    ?>