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

   if (empty($clean["FreezerID"])) {
      dconnect($link);
      header('Location: form_freeze.php?error=invalid&item=Freezer');
      exit;
   } else {
      $FreezerID= $clean["FreezerID"];
   }

   if (empty($clean["Temp"])) {
      dconnect($link);
      header('Location: form_freeze.php?error=invalid&item=Temperature');
      exit;
   } else {
      $Temp= $clean["Temp"];
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
    $time_ = strval($hour.":".$min.":00");
    $time = $date ." " .$time_; //YYYY-MM-DD hh:mm:ss

    if (empty($clean["FrFishID"]) && empty($clean["FrSample"]) && empty($clean["FrRNA"])) {
        dconnect($link);
        header('Location: form_freeze.php?error=select&item=Sample');
        exit;
    }
    if(!empty($clean["FrFishID"])) {
        if ($clean["FrFishID"] != "-1") {
            $FrFishID= $_POST["FrFishID"];
            $sql = "INSERT INTO `freeze` (`FrFreezer`, `Temperature`,`FrInTime`, `FrType`,`FrFish`) VALUES ('$FreezerID', '$Temp', '$time', 0, $FrFishID)";
            $res = mysqli_query($link, $sql);
            if (!$res) {
                dconnect($link);
                header("Location: form_freeze.php?error=sql&item=Fish");
                exit;
            }
        }
    }
    $test = $_POST['FrSample'];
    if(!empty($clean["FrSample"])) {
        if ($clean["FrSample"] != "-1") {
            $FrSample= $clean["FrSample"];
            $sql = "INSERT INTO `freeze` (`FrFreezer`, `Temperature`,`FrInTime`, `FrType`,`FrSample`) VALUES ('$FreezerID', '$Temp', '$time', 1, $FrSample)";
            $res = mysqli_query($link, $sql);
            if (!$res) {
                dconnect($link);
                header("Location: form_freeze.php?error=sql&item=Sample");
                exit;
            }
        }
    }
    if (!empty($clean["FrRNA"])) {
        if ($clean["FrRNA"] != "-1") {
            $FrRNA= $clean["FrRNA"];
            $sql = "INSERT INTO `freeze` (`FrFreezer`, `Temperature`,`FrInTime`, `FrType`,`FrRNA`) VALUES ('$FreezerID', '$Temp', '$time', 2, $FrRNA)";
            $res = mysqli_query($link, $sql);
            if (!$res) {
                dconnect($link);
                header("Location: form_freeze.php?error=sql&item=RNA");
                exit;
            }
        }
    }
    header("Location: form_freeze.php?success=added&item=Samples to freeze");
} else {
   header("Location: form_freeze.php");
}
?>