<?php
    session_start();
    include "connect.php";
    include "check_injection.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // delete account
        $link = connect();
        $clean = check_injection($link, $_POST);
        if ($clean["deltext"] == 'CONFIRMDELETE') {
            $sql = 'UPDATE account SET Email = NULL, Pwd = NULL, UserRole = NULL WHERE UserID =' . $_SESSION['UserID'];
            if (mysqli_query($link, $sql)) {
                $_SESSION['aqualogin'] = 'abort';
                $_SESSION['UserID'] = NULL;
                $_SESSION['UserEmail'] = NULL;
                $_SESSION['UserName'] = NULL;
                $_SESSION['UserRole'] = NULL;
                $_SESSION['UserLab'] = NULL;
                $_SESSION['UserLabID'] = NULL;
                $_SESSION['LabAdmin'] = NULL;
                header("Location: index.php?success=logout");
            } else {
                dconnect($link);
                header("Location: accountSettings.php?error=invalid&item=Delete confirmation text");
            }
        } else {
            dconnect($link);
            header("Location: accountSettings.php?error=invalid&item=Delete confirmation text");
        }
    } else {
        // simple logout
        $_SESSION['aqualogin'] = 'abort';
        $_SESSION['UserID'] = NULL;
        $_SESSION['UserEmail'] = NULL;
        $_SESSION['UserName'] = NULL;
        $_SESSION['UserRole'] = NULL;
        $_SESSION['UserLab'] = NULL;
        $_SESSION['UserLabID'] = NULL;
        $_SESSION['LabAdmin'] = NULL;
        header("Location: index.php?success=logout");
    }



    
?>