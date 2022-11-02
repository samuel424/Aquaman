<?php
    include "connect.php";
    include "check_injection.php";
    include "standardAssets.php";
    
    // User
    session_start();
    if (empty($_SESSION['UserID'])) {
        header('Location: login.php?error=login');
        exit;
    } else {
        $leader = $_SESSION['UserID'];
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') { //coming from register form
        $link = connect();
        $clean = check_injection($link,$_POST);
        
        // Location
        if (empty($clean['location'])) {
            dconnect($link);
            header('Location: form_rawmaterial.php?error=invalid&item=location');
            exit;
        }
        $location = $clean["location"];

        //Date
        if (empty($_POST['date'])) {
            dconnect($link);
            header('Location: form_rawmaterial.php?error=invalid&item=date');
            exit;
        } 
        $date = $_POST['date'];
        $date_ = explode('-', $date);
        if (checkdate($date_[0], $date_[1], $date_[2])) {
            dconnect($link);
            header('Location: form_rawmaterial.php?error=invalid&item=date');
            exit;
        }
        
        // Hour
        if (empty($clean['hour'])) {
            dconnect($link);
            header("Location: form_rawmaterial.php?error=invalid&item=hour");
            exit;
        }
        $hour = $clean['hour'];
        if (intval($hour) >= 24) {
            dconnect($link);
            header("Location: form_rawmaterial.php?error=outside&item=hour&max=23");
            exit;
        }
        //minute
        if (empty($clean['minute'])) {
            dconnect($link);
            header('Location: form_rawmaterial.php?error=invalid&item=minute');
            exit;
        }
        $min = $clean['minute'];
        if (intval($min) >= 60) {
            dconnect($link);
            header('Location: form_rawmaterial.php?error=outside&item=minute&max=59');
            exit;
        }
        $time = "$date $hour:$min:00 ";//YYYY-MM-DD hh:mm:ss
        
        // Chemical
        $null = -1;
        if (!empty($clean["ph"])) {
            $ph = r_non_num($clean["ph"]);
        } else {
            $ph = $null;
        }
        if (!empty($clean["oxygen"])) {
            $oxygen = r_non_num($clean["oxygen"]);
        } else {
            $oxygen = $null;
        }
        if (!empty($clean["hg"])) {
            $hg = r_non_num($clean["hg"]);
        } else {
            $hg = $null;
        }
        if (!empty($clean["pb"])) {
            $pb = r_non_num($clean["pb"]);
        } else {
            $pb = $null;
        }
        
        $sql = "INSERT INTO fieldsampling 
            (LeaderID, LocationID, STimestamp, pH, Oxygen, Hg, Pb) 
            VALUES ('$leader', '$location', '$time', '$ph', '$oxygen', '$hg', '$pb')";
        if (mysqli_query($link,$sql)){
            dconnect($link);
            header('Location: form_rawmaterial.php?success=fieldsampling');
            exit;
        } else {
            dconnect($link);
            header('Location: form_rawmaterial.php?error=sql');
            exit;
        }

    } else {
        dconnect($link);
        header('Location: form_rawmaterial.php');
        exit;
    }
    ?>