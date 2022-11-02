<?php
    include 'check_injection.php';
    include 'connect.php';
    
    // Login check
    session_start();
    if (empty($_SESSION['UserID'])) {
        dconnect($link);
        header('Location: login.php?error=login');
        exit;
    }

    $link = connect();
    $newlab = $_POST['lab'];
    $sql = "SELECT LabID, LabName FROM Laboratory WHERE LabID = $newlab";
    $result = mysqli_query($link,$sql);
    $row = mysqli_fetch_row($result);
    // set new active lab
    $_SESSION['UserLab'] = $row[1];
    $_SESSION['UserLabID'] = $row[0];

    $user = $_SESSION['UserID'];
    $lab = $_SESSION['UserLabID'];
    $sql = "SELECT LabRole FROM labaffiliation WHERE LabID = $lab AND UserID = $user";
    $result = mysqli_query($link,$sql);
    $row = mysqli_fetch_row($result);
    // set new active lab
    $_SESSION['LabAdmin'] = $row[0];

    header('Location: form_affiliation.php');
    ?>