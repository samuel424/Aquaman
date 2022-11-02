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


    if ($_SERVER['REQUEST_METHOD'] == 'POST') { //coming from register form
        $link = connect();
        $clean = check_injection($link,$_POST);
        if (empty($_POST['user'])) {
            dconnect($link);
            header('Location: form_affiliation.php?error=invalid&item=User');
            exit;
        } else {
            $user = $_POST['user'];
            $sql_u = "SELECT UserID FROM Account WHERE UserID = $user";
            $resu = mysqli_query($link,$sql_u);
            if (mysqli_num_rows($resu) < 1) {
                dconnect($link);
                header("Location: form_affiliation.php?error=invalid&item=User");
                exit;
            }

        }
        
        // Role
        if(!empty($clean["role"])) {
            $role = 1; // Admin
        } else { 
            $role = 0; // Researcher
        }

        $lab = $_SESSION['UserLabID'];
        $sql = "INSERT INTO LabAffiliation (UserID, LabID, LabRole) VALUES ($user, $lab, $role)";

        if (mysqli_query($link,$sql)){
            dconnect($link);
            header ('Location: form_affiliation.php?success=added&item=Labaffiliation');
            exit;
        }else {
            $error = mysqli_error($link);
            dconnect($link);
            header ("Location: form_affiliation.php?error=sql$error");
            exit;
        }

    } else {
        dconnect($link);
        header ('Location: form_affiliation.php');
        exit;
    }
?>