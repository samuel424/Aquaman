<?php
include 'connect.php';
include "check_injection.php";

// User
session_start();
if (empty($_SESSION['UserID'])) {
    header('Location: login.php?error=login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {  
    $link=connect();
    $clean = check_injection($link, $_POST); 

    if (empty($clean['freezeID'])) {
        dconnect($link);
        header('Location: form_freeze.php?error=invalid&item=Frozen sample');
        exit;
    } elseif ($clean["freezeID"] == "-1") {
        dconnect($link);
        header('Location: form_freeze.php?error=invalid&item=Frozen sample');
        exit;
    } else {
        $freeze_id = $clean["freezeID"];
    }

    //Date
   if (empty($clean['date'])) {
    dconnect($link);
    header('Location: form_freeze.php?error=invalid&item=date');
    exit;
    } 
    $date = $clean['date'];
    $date_ = explode('-', $date);
    if (checkdate($date_[0], $date_[1], $date_[2])) {
        dconnect($link);
        header('Location: form_freeze.php?error=invalid&item=date');
        exit;
    }

    // Hour
    if (empty($clean['hour'])) {
        dconnect($link);
        header("Location: form_freeze.php?error=invalid&item=hour");
        exit;
    }
    $hour = $clean['hour'];
    if (intval($hour) > 24) {
        dconnect($link);
        header("Location: form_freeze.php?error=outside&item=hour&max=23");
        exit;
    }
    //minute
    if (empty($clean['minute'])) {
        dconnect($link);
        header('Location: form_freeze.php?error=invalid&item=minute');
        exit;
    }
    $min = $clean['minute'];
    if (intval($min) > 60) {
        dconnect($link);
        header('Location: _freeze.php?error=outside&item=minute&max=59');
        exit;
    }
    $time = strval($hour.":".$min.":00");
    $outtime = $date ." " .$time; //YYYY-MM-DD hh:mm:ss

    $sql = "UPDATE Freeze SET FrOutTime = '$outtime' WHERE FreezeID = $freeze_id";
    $res = mysqli_query($link, $sql);
    if (!$res) {
        echo mysqli_error($link);
        dconnect($link);
        header("Location: form_freeze.php?error=sql");
        exit;
    } else {
        dconnect($link);
        header("Location: form_freeze.php?success=freezer_out");
        exit;
    }

}