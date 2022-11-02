<?php
    session_start();
    include "connect.php";
    include "check_injection.php"; 
    
    // Connect Database
    $link = connect();
    $clean = check_injection($link, $_POST);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // test for which form was posted
        if (array_key_exists('data_submit', $_POST)) {
            $changedata = array();
            // Emailadress
            if (empty($clean["email"])) {
            } elseif (strlen($clean["email"]) > 320) {
                dconnect($link);
                header("Location: accountSettings.php?error=long&item=Email address&max=320");
                exit;
            } else {
                $email = $clean["email"];
                $email = filter_var($email, FILTER_SANITIZE_EMAIL);
                $changedata['Email'] = $email;
            }

            // Firstname
            if (empty($clean["fname"])) {
            } elseif (strlen($clean["fname"]) > 30) {
                dconnect($link);
                header("Location: accountSettings.php?error=long&item=First name&max=30");
                exit;
            } else {
                $fname = $clean["fname"];
                $changedata['Firstname'] = $fname;
            }

            // Lastname
            if (empty($clean["lname"])) {
            } elseif (strlen($clean["lname"]) > 30) {
                dconnect($link);
                header("Location: accountSettings.php?error=long&item=Last name&max=30");
                exit;
            } else {
                $lname = $clean["lname"];
                $changedata['Lastname'] = $lname;
            }

            // test for any changes
            if (count($changedata) == 0) {
                dconnect($link);
                header("Location: accountSettings.php");
                exit;
            } else {
                // create correct SQL statement
                $sql = "UPDATE account SET";
                $changenr = 0;
                foreach ($changedata as $col => $val) {
                    $changenr = $changenr + 1;
                    if ($changenr == 1) {
                        $sql = $sql . $col . ' = ' . $val;
                    } else {
                        $sql = $sql . ', ' . $col . ' = ' . $val;
                    }
                }
                $sql = $sql . "WHERE UserID = " . $_SESSION['UserID'];
                // enter into database
                mysqli_query($link, $sql);
                dconnect($link);

                // update session variables
                session_start();
                $link = connect();
                $sql = "SELECT Firstname, Lastname, Email FROM account WHERE UserID = " . $_SESSION['UserID'];
                $result = mysqli_query($link, $sql);
                $result = mysqli_fetch_row($result);
                $_SESSION['UserEmail'] = $result[2];
                $_SESSION['UserName'] = $result[0] . ' ' . $result[1];

                // send back
                dconnect($link);
                header("Location: accountSettings.php?success=added&item=Changes");
                exit;
            }

        } elseif (array_key_exists('pwd_submit', $_POST)) {
            // Old Password
            if (empty($_POST["curr_pwd"])) {
                dconnect($link);
                header("Location: accountSettings.php?error=invalid&item=Password");
                exit;
            } else {
                $old_pwd = $_POST["curr_pwd"];
            }

            // New Password
            if (empty($_POST["new_pwd"])) {
                dconnect($link);
                header("Location: accountSettings.php?error=invalid&item=Password");
                exit;
            } else {
                $pwd = $_POST["new_pwd"];
            }

            // ConfirmPassword
            if (empty($_POST["conf_pwd"])) {
                dconnect($link); 
                header("Location: accountSettings.php?error=invalid&item=Confirm password");
                exit;
            } else {
                $cpwd = $_POST["conf_pwd"];
            }

            // check for existing account
            $sql = "SELECT * FROM account WHERE UserID = " . $_SESSION['UserID'];
            echo $sql;
            $result = mysqli_query($link, $sql); 
            if (mysqli_num_rows($result) == 1) {
                $result = mysqli_fetch_row($result);
                $pwdcall = $result[4];
                //check if corect hash
                if (password_verify($old_pwd, strval($pwdcall))) {
                    // check if comfirm password is the same
                    if ($pwd == $cpwd) {
                        //Check password complex enought
                        include 'pwd_complex.php';
                        if (pwd_complex($pwd)) {
        
                            // hash password    
                            $hpwd = password_hash($pwd, PASSWORD_DEFAULT);
            
                            // store in table
                            $sql = "UPDATE account SET Pwd = $hpwd WHERE UserID = " . $_SESSION['UserID'];
                            mysqli_query($link, $sql);
                            
                            // send back
                            dconnect($link);
                            header("Location: accountSettings.php?success=added&item=Password");
                            exit;
        
                        } else {
                            // password not complex enough
                            dconnect($link);
                            header("Location: accountSettings.php?error=complex&item=Password");
                            exit;
                        }
                    } else {
                        // passwords not the same
                        dconnect($link);
                        header("Location: accountSettings.php?error=confirm_pwd");
                        exit;
                    }
                } else {
                    // invalid password
                    dconnect($link);
                    header("Location: accountSettings.php?error=invalid_creds");
                }
            } else {
                //invalid user
                dconnect($link);
                header("Location: accountSettings.php?error=invalid_creds");
            }

        }   
        
    }
?>