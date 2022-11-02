<?php
include 'connect.php';
include 'check_injection.php';

// User
session_start();
if (empty($_SESSION['UserID'])) {
    header('Location: login.php?error=login');
    exit;
} else {
    $lmainaccount = $_SESSION['UserID'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') { //coming from register form
    $link = connect();
    $clean = check_injection($link, $_POST);

    // Name
    if (empty($clean["LabName"])) {
        dconnect($link);
        header ("Location: form_laboratory.php?error=invalid&item=laboratory Name");
        exit;
    } elseif (strlen($clean["LabName"]) > 30) {
        dconnect($link);
        header ("Location: form_laboratory.php?error=long&item=laboratory_Name&max=30");
        exit;
    } else {
        $labname = $clean["LabName"];
    }

    //Country
    if (empty($clean["Country"])) {
        $lcountry = '';
    } elseif (strlen($clean["Country"]) > 40) {
        dconnect($link);
        header ("Location: form_laboratory.php?error=long&item=Country&max=40");
        exit;
    } else {
        $lcountry = $clean["Country"];
    }
    
    
    //City
    if (empty($clean["City"])) {
        $lcity = '';
    } elseif (strlen($clean["City"]) > 40) {
        dconnect($link);
        header ("Location: form_laboratory.php?error=long&item=City&max=40");
        exit;
    } else {
        $lcity = $clean["City"];
    }
    
    
    //Adress
    if (empty($clean["LabAddress"])) {
        $labaddress = '';
    } elseif (strlen($clean["LabAddress"]) > 100) {
        dconnect($link);
        header ("Location: form_laboratory.php?error=long&item=Adress&max=100");
        exit;
    } else {
        $labaddress = $clean["LabAddress"];
    }

    // check if in db
    $check_lab = "SELECT * FROM laboratory WHERE LabName = '$labname'";
    $result = mysqli_query($link, $check_lab);
    if (mysqli_num_rows($result) > 0){
        dconnect($link);
        header ('Location: form_laboratory.php?error=exists&item=laboratory');
        exit;
    }

    // insert
    $sql = "INSERT INTO laboratory (LabName, Country, City, LabAddress, MainAccount) VALUES ('$labname', '$lcountry', '$lcity', '$labaddress', '$lmainaccount')";
    if(mysqli_query($link, $sql)){

        $sql = "SELECT LabID FROM laboratory WHERE LabName = '$labname'";
        $result = mysqli_query($link, $sql);
        $result = mysqli_fetch_row($result);
        $labid = $result[0];

        $sql = "INSERT INTO labaffiliation (UserID, LabID, LabRole) VALUES ($lmainaccount, $labid, 1)";
        if(mysqli_query($link, $sql)){
            dconnect($link);
            header('Location: form_laboratory.php?success=laboratory');
            exit;

        } else{
            dconnect($link);
            header('Location: form_laboratory.php?error=sql');
            exit;
        }
    } else{
        dconnect($link);
        header('Location: form_laboratory.php?error=sql');
        exit;
    } 
} else {
    dconnect($link);
    header("Location: form_laboratory.php");
}

?>
