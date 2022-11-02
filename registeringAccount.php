<?php
    session_start();
    include "connect.php";
    include "check_injection.php";
    include "mailtemplate.php";

    // Connect Database
    $link = connect();


    if ($_SERVER['REQUEST_METHOD'] == 'POST') { //coming from register form
         
        $clean = check_injection($link, $_POST);

        /* Captcha*/
        if ($clean['captcha_challenge'] != $_SESSION['captcha_text']){
            dconnect($link);
            header("Location: register.php?error=captcha");
            exit;
        }
        // Emailadress
        if (empty($clean["Emailaddress"])) {
            dconnect($link);
            header("Location: register.php?error=invalid&item=Email address");
            exit;
        } elseif (strlen($clean["Emailaddress"]) > 320) {
            dconnect($link);
            header("Location: register.php?error=long&item=Email address&max=320");
            exit;
        } else {
            $email = $clean["Emailaddress"];
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        }
        // Password
        if (empty($_POST["Password"])) {
            dconnect($link);
            header("Location: register.php?error=invalid&item=Password");
            exit;
        } else {
            $pwd = $_POST["Password"];
        }

        // Firstname
        if (empty($clean["FirstName"])) {
            dconnect($link);
            header("Location: register.php?error=invalid&item=First name");
        } elseif (strlen($clean["FirstName"]) > 30) {
            dconnect($link);
            header("Location: register.php?error=long&item=First name&max=30");
            exit;
        } else {
            $fname = $clean["FirstName"];
        }

        // Lastname
        if (empty($clean["LastName"])) {
            dconnect($link);
            header("Location: register.php?error=invalid&item=Last name");
            exit;
        } elseif (strlen($clean["LastName"]) > 30) {
            dconnect($link);
            header("Location: register.php?error=long&item=Last name&max=30");
            exit;
        } else {
            $lname = $clean["LastName"];
        }
        
        // ConfirmPassword
        if (empty($_POST["ConfirmPassword"])) {
            dconnect($link); 
            header("Location: register.php?error=invalid&item=Confirm password");
            exit;
        } else {
            $cpwd = $_POST["ConfirmPassword"];
        }
                            
            
        // Validates Email is correct format
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Check if email already has account
            //echo "hoon";
            $sql1 = "SELECT UserID FROM account WHERE Email = '$email'";
            $sql2 = "SELECT TempID FROM temporary WHERE Email = '$email'";

            $result1 = mysqli_query($link, $sql1);
            $result2 = mysqli_query($link, $sql2);
            //$rcnt = $result->num_rows;
            //echo $rcnt;


            if (mysqli_num_rows($result1) > 0) {
                header("Location: register.php?error=invalid&item=Email address");
            } elseif (mysqli_num_rows($result2) > 0) {
                header("Location: register.php?error=invalid&item=Email address");
            } else {
                // Check password == conformPassword
                if ($pwd == $cpwd) {
                    //Check password complex enought
                    include 'pwd_complex.php';
                    if (pwd_complex($pwd)) {

                        // hash password    
                        $hpwd = password_hash($pwd, PASSWORD_DEFAULT);

                        // make confirm hash
                        $confbit = random_bytes(64);
                        $confhash = hash('sha256', $confbit);
        
                        // store in tempoary table
                        $sql_insert_temporary='INSERT INTO
                        temporary (FIrstname, Lastname, Email, Pwd, confirmhash) 
                        VALUES ("' . $fname . '", "' . $lname . '", "' . $email . '",
                         "' . $hpwd . '", "' . $confhash . '")';
                        mysqli_query($link, $sql_insert_temporary);

                        // send conformation email
                        $maillink = 'localhost/aquaman/registerConfirm.php?hash=' . $confhash;
                        $mail->Body    = '
                            <html>
                            <head>
                                <title>Aquaman Account Comfirmation</title>
                            </head>
                            <body>
                                <p>Click the link to activate your account!</p>
                                <a href="' . $maillink . '" title="localhost/aquaman/registerConfirm.php">Activate Account</a>
                            </body>
                            </html>';
                        $mail->addAddress($email, $fname);
                        try {
                            $mail->send();
                        } catch (Exception $e) {
                            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                        }
                        dconnect($link);
                        header("Location: registerConfirm.php?hash=sent");
                        exit;
                        
                    } else {
                        dconnect($link);
                        header("Location: register.php?error=complex&item=Password");
                        exit;
                    }
                } else {
                    dconnect($link);
                    header("Location: register.php?error=confirm_pwd");
                    exit;
                }
            } 
        } else {
            dconnect($link);
            header("Location: register.php?error=invalid&item=Email address");
            exit;
        }
    } else {
        dconnect($link);
        header("Location: register.php");
        exit;
    }
?>