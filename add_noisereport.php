<?php
    include "connect.php";
    include "check_injection.php";

    // Login check
    session_start();
    if (empty($_SESSION['UserID'])) {
        header('Location: login.php?error=login');
        exit;
    } else {
        $noise_reporter = $_SESSION['UserID'];
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') { //coming from register form
        $link = connect();
        $clean = check_injection($link, $_POST);
                
        // Location
        if (empty($clean["location"])) {
            dconnect($link);
            header('Location: form_noise.php?error=invalid&item=locationID');
            exit();
        } else {
            $noise_locationID = $clean["location"];
        }
        
        // Date
        if (empty($_POST['date'])) {
            dconnect($link);
            header('Location: form_noise.php?error=invalid&item=date');
            exit();
        } else {
            $noise_date = $_POST['date'];
            $date_ = explode('-', $noise_date);
            $noise_year = $date_[0];
            $noise_month = $date_[1];
            $noise_day = $date_[2];
        }
        if (!checkdate($noise_month, $noise_day, $noise_year)) {
            dconnect($link);
            header('Location: form_noise.php?error=invalid&item=Date');
            exit;
        }
        
        // Noise
        
        if (empty($clean["rank"])) {
            dconnect($link);
            header('Location: form_noise.php?error?error=invalid&item=Noiserank');
            exit;
        } else {
            $noise_rank = $clean["rank"];
        }
        if ($noise_rank > 10){
            dconnect($link);
            header('Location: form_noise.php?error=outside&item=rank&max=10&min=0');
            exit;
        }
        $forbidden = '/[^0123456789.-]/i';
        if (preg_match($forbidden, $noise_rank)) {
            dconnect($link);
            header('Location: form_noise.php?error?error=invalid&item=Noiserank');
            exit;
        }
        
        // Insert
        $sql = "INSERT INTO noise(LocationID, NDate, ReporterID, NoiseRank) VALUES($noise_locationID, '$noise_date', $noise_reporter, $noise_rank)"; //combine full date into one variable
        
        if (mysqli_query($link,$sql)){
            dconnect($link);
            header ('Location: form_noise.php?success=added&item=Noisereport');
            exit;
        }else {
            dconnect($link);
            header ('Location: form_noise.php?error=sql');
            exit;
        }
        

    } else {
        dconnect($link);
        header("Location: form_noise.php");
        exit;
    }


?>