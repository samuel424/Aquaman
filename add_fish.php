<?php
include 'connect.php';
include 'check_injection.php';

// User
session_start();
if (empty($_SESSION['UserID'])) {
    header('Location: login.php?error=login');
    exit;
} else {
    $userID = intval($_SESSION['UserID']);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $link = connect();
    $clean = check_injection($link, $_POST);

    // Expedition
    if (empty($clean['fieldID'])) {
        dconnect($link);
        header('Location: form_fish.php?error=invalid&item=Expedition');
        exit;
    } else {
        $expedition = intval($clean['fieldID']);
    }
    // Do a check real exp

    // Catchmethod
    if (empty($clean['catchmethodID'])) {
        dconnect($link);
        header('Location: form_fish.php?error=invalid&item=Catch method');
        exit;
    } else {
        $catchmethod = intval($clean['catchmethodID']);
    }

    // Species
    if (empty($clean['speciesID'])) {
        dconnect($link);
        header('Location: form_fish.php?error=invalid&item=Species');
        exit;
    } else {
        $species = intval($clean['speciesID']);
    }
    
    $sexes = [];
    
    if (empty($clean['ch_male'] )) {
        $sexes['M'] = 0;
    } elseif ($clean['ch_male'] == 'A'){
        $sexes['M'] = intval($clean['n_male']);
    } else {
        $sexes['M'] = 0;
    }
    
    if (empty($clean['ch_female'] )) {
        $sexes['F'] = 0;
    } elseif ($clean['ch_female'] == 'B'){
        $sexes['F'] = intval($clean['n_female']);
    } else {
        $sexes['F'] = 0;
    }
    
    foreach ($sexes as $key => $value){
        for ($i = 0; $i < $value; $i++){
            $insert = mysqli_query($link, "INSERT INTO FishIndividual (FishSamplingID, Sex, Species, Catchmethod, EnteredByUser)
            VALUES ($expedition, '$key', $species, $catchmethod, $userID)");
            if (!$insert){ 
                dconnect($link);
                header('Location: form_fish.php?error=sql&' . mysqli_error($link));
            } 
        }
    }
    
    header('Location: form_fish.php?success=fish&nm='.$sexes['M'].'&nf='.$sexes['F']);
    dconnect($link);
}


?>