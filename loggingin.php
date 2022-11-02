<?php
    session_start();
    include "connect.php"; //connection to db needed for check_injection.php
    include "check_injection.php"; //function for checking _POST data via real_escape
    $link = connect();
    $clean = check_injection($link, $_POST); 

    /* Captcha*/
    if ($clean['captcha_challenge'] != $_SESSION['captcha_text']){
        dconnect($link);
        header("Location: login.php?error=captcha");
    } else {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // check for empty input
            if (!empty($clean["Emailaddress"])) {
                $email = $clean["Emailaddress"];
                $email = filter_var($email, FILTER_SANITIZE_EMAIL);
                if (!empty($_POST["Password"])) {
                    $pwd = $_POST["Password"];

                    // check for existing account
                    $result = mysqli_query($link, "SELECT * FROM account WHERE Email = '" . $email . "'"); 
                    if (mysqli_num_rows($result) == 1) {
                        $result = mysqli_fetch_row($result);
                        $pwdcall = $result[4];
                        //check if corect hash
                        if (password_verify($pwd, strval($pwdcall))) {
                            // login
                            session_start();
                            $_SESSION['aqualogin'] = 'wearein';
                            // set session variables
                            $_SESSION['UserID'] = $result[0];
                            $_SESSION['UserEmail'] = $result[3];
                            $_SESSION['UserName'] = $result[1] . ' ' . $result[2];
                            $_SESSION['UserRole'] = $result[5];
                            $result = mysqli_query($link, "SELECT LabName, laboratory.LabID, LabRole FROM labaffiliation LEFT JOIN laboratory ON labaffiliation.LabID = laboratory.LabID WHERE UserID = '" . $_SESSION['UserID'] . "'");
                            if (mysqli_num_rows($result) > 0) {
                                $result = mysqli_fetch_row($result);
                                $_SESSION['UserLab'] = $result[0];
                                $_SESSION['UserLabID'] = $result[1];
                                $_SESSION['LabAdmin'] = $result[2];
                            } else {
                                $_SESSION['UserLab'] = 'None';
                                $_SESSION['UserLabID'] = NULL;
                                $_SESSION['LabAdmin'] = 0;
                                
                            }

                            // send to homepage
                            header("Location: index.php?success=login");
                        } else {
                            dconnect($link);
                            header("Location: login.php?error=invalid_creds");
                        }
                    } else {
                        // invalid email
                        dconnect($link);
                        header("Location: login.php?error=invalid_creds");
                    }

                } else { 
                    // Password unset
                    dconnect($link);
                    header("Location: login.php?error=invalid");
                }
            } else { 
                // Emailaddress unset
                dconnect($link);
                header("Location: login.php?error=invalid");
            }
        }
    }
?>