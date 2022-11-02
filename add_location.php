<?php
include 'connect.php';
include 'check_injection.php';

// Login check
session_start();
if (empty($_SESSION['UserID'])) {
    header('Location: login.php?error=login');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') { //coming from register form
    $link = connect();
    $clean = check_injection($link, $_POST);

    // Location Name
    if (empty($clean["loc_name"])) {
        dconnect($link);
        header("Location: form_location.php?error=invalid&item=Location name");
        exit;
    } elseif(strlen($clean["loc_name"]) > 30) {
        dconnect($link);
        header("Location: form_location.php?error=long&item=Location name&max=30");
        exit;
    } else {
        $loc_name = $clean["loc_name"];
    }

    //cordinates
    $forbidden = '/[^0123456789.-]/i';
    if (empty($clean["cor_lat"]) or empty($clean["cor_long"]) or (abs(intval($clean['cor_lat'])) > 90) or (abs(intval($clean['cor_long'])) > 180)) {
        dconnect($link);
        header('Location: form_location.php?error=coordinate');
        exit;
    } else {
    $cor_lat = $clean["cor_lat"];
    $cor_long = $clean["cor_long"];
    }

    //no letters in the coordinate values
    if(preg_match($forbidden, $cor_lat) or preg_match($forbidden, $cor_long)) {
        dconnect($link);
        header('Location: form_location.php?error=lettersentered');
        exit;
    }
    

    //Checks that no location with same coordinates
    $check_coor = "SELECT * FROM SamplingLocation WHERE CorLatitude = '$cor_lat' AND CorLongitude = '$cor_long'";
    $result = mysqli_query($link, $check_coor);
    if (mysqli_num_rows($result) > 0){
        dconnect($link);
        header ('Location: form_location.php?error=exists&item=Location');
        exit;
    }

    // Attempt insert table query execution
    $sql = "INSERT INTO SamplingLocation(LocationName, CorLatitude, CorLongitude)
    VALUES('$loc_name', '$cor_lat', '$cor_long')";
    
    if(mysqli_query($link, $sql)){
        $number = 1;
        dconnect($link);
        header('Location: form_location.php?success=location&number='.$number);
        exit;
    } else{
        dconnect($link);
        header('Location: form_location.php?error=sql');
        exit;
    }
}

?>