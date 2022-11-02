<?php
    include "connect.php";
    include "check_injection.php";

    // Logincheck
    session_start();
    if (empty($_SESSION['UserID'])) {
        header('Location: login.php?error=login');
        exit;
    }
    // Connect Database
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') { // checks that coming from register form
        $link = connect();
        $clean = check_injection($link, $_POST);

        // Method
        if (empty($clean["Method"])) {
            dconnect($link);
            header("Location: form_catchmethod.php?error=invalid&item=Cathcmethod");
            exit;
        } elseif (strlen($clean["Method"]) > 50) {
            dconnect($link);
            header("Location: form_catchmethod.php?error=long&item=Cathcmethod&max=50");
            exit;
        }else {
            $Cmethod = $clean["Method"];
        }
        
        // Check if already exists
        $check_sql = "SELECT * FROM catchmethod WHERE CatchmethodName = '$Cmethod'";
        $result1 = mysqli_query($link, $check_sql);
        if (($result1->num_rows) > 0) {
            dconnect($link);
            header("Location: form_catchmethod.php?error=exists&item=Capturemethod");
            exit;
        }
        
        // Add the method
        $sql = "INSERT INTO catchmethod (CatchmethodName) VALUES('$Cmethod')";
        $res = mysqli_query($link, $sql);
        if ($res) {
            dconnect($link);
            header("Location: form_catchmethod.php?success=added&item=Catchmethod");
        } else {
            dconnect($link);
            header("Location: form_catchmethod.php?error=sql");
        }
    }
?>