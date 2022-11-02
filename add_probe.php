<?php
    include 'check_injection.php';
    include 'connect.php';
    
    // Login check
    session_start();
    if (empty($_SESSION['UserID'])) {
        header('Location: login.php?error=login');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') { //coming from register form
        $link = connect();
        $clean = check_injection($link,$_POST);

        // Name
        if (empty($clean['name'])) {
            dconnect($link);
            header("Location: form_probe.php?error=invalid&item=Probename");
            exit;
        } elseif (strlen($clean['name']) > 30) {
            dconnect($link);
            header("Location: form_probe.php?error=long&item=Probename&max=10");
            exit;
        }else {
            $ProbeName = $clean['name'];
        }
        //Type
        if (empty($clean['type'])) {
            dconnect($link);
            header("Location: form_probe.php?error=invalid&item=Probe type");
            exit;
        } elseif (preg_match("/{$clean['type']}/i", 'FRP') == FALSE) {
            dconnect($link);
            header("Location: form_probe.php?error=invalid&item=Probe type");
            exit;
        } else {
            $type = $clean['type']; 
        }
        //Fluorophore
        if (($clean['type'] == 'P') and empty($clean['fluor'])) {
            dconnect($link);
            header("Location: form_probe.php?error=invalid&item=fluorophore");
            exit;
        } elseif (strlen($clean['fluor']) > 10) {
            dconnect($link);
            header("Location: form_probe.php?error=long&item=fluorophore&max=10");
            exit;
        } else {
            $fluor = $clean['fluor']; 
        }
        // Sequence
        if (empty($clean['seq'])) {
            dconnect($link);
            header("Location: form_probe.php?error=invalid&item=Sequence");
            exit;
        } elseif (strlen($clean['seq']) > 40) {
            dconnect($link);
            header("Location: form_probe.php?error=long&item=Sequence&max=40");
            exit;
        }else {
            $Seq = $clean['seq'];
        }
        // Targetgene
        if (empty($clean['Target'])) {
            dconnect($link);
            header("Location: form_probe.php?error=invalid&item=Target");
            exit;
        }else {
            $Target = $clean['Target'];
        }
            
        // Insert
        $sql= "INSERT INTO Probe (ProbeName, ProbeSequence, TargetGene, PrType, Fluor) VALUES ('$ProbeName', '$Seq', $Target, '$type', '$fluor')";
        
        if (mysqli_query($link,$sql)){
            dconnect($link);
            header ('Location: form_probe.php?success=added&item=Probe/Primer');
            exit;
        } else {
            dconnect($link);
            header ('Location: form_probe.php?error=sql');
            exit;
        } 
    } else {
        dconnect($link);
        header("Location: form_probe.php");
        exit;
    }

?>