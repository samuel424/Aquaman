<?php
	include "connect.php";
	include "check_injection.php";
    include "standardAssets.php";
    
    // Login check
    session_start();
    if (empty($_SESSION['UserID'])) {
        header('Location: login.php?error=login');
        exit;
    } else {
        $User = $_SESSION['UserID'];
    }
    
    
	if ($_SERVER['REQUEST_METHOD'] == 'POST') { //coming from register form
        $link = connect();
        $clean = check_injection($link,$_POST);
        
        // RSampleID
        if (empty($clean['checkbox'])) {
            dconnect($link);
            header('Location: form_RNA.php?error=invalid&item=checkbox');
            exit;
        }

        //Date
        if (empty($_POST['date'])) {
            dconnect($link);
            header('Location: form_RNA.php?error=invalid&item=date');
            exit;
        } 
        $date = $_POST['date'];
        $date_ = explode('-', $date);
        if (checkdate($date_[0], $date_[1], $date_[2])) {
            dconnect($link);
            header('Location: form_RNA.php?error=invalid&item=date');
            exit;
        }
        
        // Hour
        if (empty($clean['hour'])) {
            dconnect($link);
            header("Location: form_RNA.php?error=invalid&item=hour");
            exit;
        }
        $hour = $clean['hour'];
        if (intval($hour) >= 24) {
            dconnect($link);
            header("Location: form_RNA.php?error=outside&item=hour&max=23");
            exit;
        }
        //minute
        if (empty($clean['minute'])) {
            dconnect($link);
            header('Location: form_RNA.php?error=invalid&item=minute');
            exit;
        }
        $min = $clean['minute'];
        if (intval($min) >= 60) {
            dconnect($link);
            header('Location: form_RNA.php?error=outside&item=minute&max=59');
            exit;
        }
        $time = "$date $hour:$min"; //YYYY-MM-DD hh:mm
        
        // Lab
        if (empty($clean['ExtractionLabID'])) {
            dconnect($link);
            header('Location: form_RNA.php?error=invalid&item=ExtractionLabID');
            exit;
        }
        $Lab = $clean["ExtractionLabID"];

        // Extraction kit
        if (empty($clean['ExtractionKit'])) {
            dconnect($link);
            header('Location: form_RNA.php?error=invalid&item=ExtractionKit');
            exit;
        }
        $ExtractionKit = $clean["ExtractionKit"];

        // Extraction kit lot number
        if (empty($clean['ExtractionKitLotnumber'])) {
            dconnect($link);
            header('Location: form_RNA.php?error=invalid&item=ExtractionKitLotnumber');
            exit;
        }
        $ExtractionKitLotnumber = $clean["ExtractionKitLotnumber"];

        // DNaseKit
        if (empty($clean['DNaseKit'])) {
            dconnect($link);
            header('Location: form_RNA.php?error=invalid&item=DNaseKit');
            exit;
        }
        $DNaseKit = $clean["DNaseKit"];

        // DNaseLot
        if (empty($clean['DNaseLot'])) {
            dconnect($link);
            header('Location: form_RNA.php?error=invalid&item=DNaseLot');
            exit;
        }
        $DNaseLot = $clean["DNaseLot"];

        $number = count($_POST['checkbox']);
        
        foreach ($_POST['checkbox'] as $RSampleID) {
            $sql = "INSERT INTO `RNASample` (`RNAID`, `RSampleID`, `ExtractionTimestamp`, `ExtractionUserID`, `ExtractionLabID`, `ExtractionKit`, `ExtractionKitLotnumber`, `DNaseKit`, `DNaseLot`) 
                VALUES (NULL, '$RSampleID', '$time', '$User', '$Lab', '$ExtractionKit', '$ExtractionKitLotnumber', '$DNaseKit', '$DNaseLot')";
                
            if (mysqli_query($link,$sql)){
                header('Location: form_RNA.php?success=RNAExtraction&number='.$number);
            } else {
                header('Location: form_RNA.php?error=sql');
            }
        }
        dconnect($link);

    } else {
          dconnect($link);
          header('Location: form_RNA.php');
          exit;
    }
    
?>