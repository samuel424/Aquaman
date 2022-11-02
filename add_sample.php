<?php
include 'connect.php';
include 'check_injection.php';

// Login check
session_start();
if (empty($_SESSION['UserID'])) {
    header('Location: login.php?error=login');
    exit;
} else {
    $userID = $_SESSION['UserID'];
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') { //coming from register form
    $link = connect();
    $clean = check_injection($link, $_POST);

    // Tissue type
    if ($_POST['ch_other'] == 1){
        $_POST['ch_other'] = $_POST['text_other'];
    }
    $i = 0;
    $tissues = [];
    foreach($clean as $key => $value) {
        if (strpos($key, 'ch_') === 0) {
            $tissues[$i] = $value;
            $i = $i + 1;
        }
    }

    // Fish
    if (empty($clean['s_fishID'])) {
        dconnect($link);
        header('Location: form_sample.php?error=invalid&item=Fish');
        exit;
     } else {
        $fish = $clean['s_fishID'];
     }
    $check_fish = "SELECT * FROM FishIndividual WHERE FishID = $fish";
    if (mysqli_num_rows(mysqli_query($link, $check_fish)) == 0){
        header('Location: form_sample.php?error=nofish');
    }

    //Lab
    if (empty($_SESSION['UserLabID'])) {
       dconnect($link);
       header('Location: form_sample.php?error=invalid&item=Lab');
       exit;
    } else {
        $lab = intval($_SESSION['UserLabID']);
    }

    //Date
    if (empty($_POST['date'])) {
       dconnect($link);
       header('Location: form_sample.php?error=invalid&item=date');
       exit;
    } else {
        $date = $_POST['date'];
    }
    $date_ = explode('-', $date);
    if (checkdate($date_[0], $date_[1], $date_[2])) {
        dconnect($link);
        header('Location: form_sample.php?error=invalid&item=date');
        exit;
    }
    // Hour
    if (empty($clean['hour'])) {
        dconnect($link);
        header("Location: form_sample.php?error=invalid&item=hour");
        exit;
    }
    $hour = $clean['hour'];
    if (intval($hour) >= 24) {
        dconnect($link);
        header("Location: form_sample.php?error=outside&item=hour&max=23");
        exit;
    }
    //minute
    if (empty($clean['minute'])) {
        dconnect($link);
        header('Location: form_sample.php?error=invalid&item=minute');
        exit;
    }
    $min = $clean['minute'];
    if (intval($min) >= 60) {
        dconnect($link);
        header('Location: form_sample.php?error=outside&item=minute&max=59');
        exit;
    }
    $time = "$date $hour:$min:00 ";//YYYY-MM-DD hh:mm:ss
    
    $i = 0;
    foreach($tissues as $key => $value) {
        $type = $tissues[$i];
        
        $sql = "INSERT INTO Ssample(SFishID, DissectTime, DissectUser, SType, SLabID)
            VALUES('$fish', '$time', $userID, '$type', $lab)";
      
            if(mysqli_query($link, $sql)){
                $number = sizeof($tissues);
                header('Location: form_sample.php?success=sample&number='.$number);
            } else{
                header('Location: form_sample.php?error=sql&') . mysqli_error($link);
            } 
            
        $i = $i + 1;
    }
    
    dconnect($link);
} else {
    dconnect($link);
    header("Location: form_sample.php");
    exit;
}
?>