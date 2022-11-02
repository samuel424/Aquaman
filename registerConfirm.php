<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Åquamän</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <?php 
        // If loged in header('Location:index.php')
        
        include "standardAssets.php"; pageheader();
    ?> 

    <!--Content-->
    <div class="page">
    <?php
        session_start();
        include "connect.php"; //connection to db needed for check_injection.php
        include "check_injection.php"; //function for checking _POST data via real_escape
        $link = connect();
        $clean = check_injection($link, $_GET);

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            // if sent from register page
            if ($_GET['hash'] == 'sent') {
                echo 'We have sent you an email to activate your account.<br><br>';
                echo 'It may take up to 5 min to receive the email.<br>';
                echo "Don't forget to check your spam folder if you cannot find it.<br>";
            } else {
                // prepare select
                $sqlselect = "SELECT Firstname, Lastname, Email, Pwd FROM temporary WHERE confirmhash = ?";
                if ($sqlexec = mysqli_prepare($link, $sqlselect)){
                    mysqli_stmt_bind_param($sqlexec, "s", $hash);
                    $hash = $clean['hash'];
                    // test if hash is correct
                    if (mysqli_stmt_execute($sqlexec)){
                        // if we have a result, store the data
                        mysqli_stmt_store_result($sqlexec);
                        if (mysqli_stmt_num_rows($sqlexec) == 1) {
                            mysqli_stmt_bind_result($sqlexec, $fname, $lname, $email, $pwd);
                            mysqli_stmt_fetch($sqlexec);
                            mysqli_stmt_close($sqlexec);
                        
                            // prepare transaction: move from temporary into account
                            mysqli_query($link, 'START TRANSACTION');
                            $sqladd = "INSERT INTO account (Firstname, Lastname, Email, Pwd) VALUES (?, ?, ?, ?)";
                            $sqldel = "DELETE FROM temporary WHERE confirmhash = ?";
                            if ($sqladdexec = mysqli_prepare($link, $sqladd)){
                                mysqli_stmt_bind_param($sqladdexec, 'ssss', $fname, $lname, $email, $pwd);
                                if ($sqldelexec = mysqli_prepare($link, $sqldel)){
                                    mysqli_stmt_bind_param($sqldelexec, 's', $hash);
                                    
                                    // try to execute
                                    if (mysqli_stmt_execute($sqladdexec)){
                                        if (mysqli_stmt_execute($sqldelexec)){
                                            echo "Account activated. You can now log in.";
                                        } else {
                                            mysqli_query($link, 'ROLLBACK');
                                            echo "Could not activate account. Please contact an administrator.";
                                        }
                                    } else {
                                        mysqli_query($link, 'ROLLBACK');
                                        echo "Could not activate account. Please contact an administrator.";
                                    }
                                } else {
                                    mysqli_query($link, 'ROLLBACK');
                                }
                            } else {
                                mysqli_query($link, 'ROLLBACK');
                            }
                        } else {
                            echo "Could not activate account. Please contact an administrator.";
                        }
                    } else {
                        echo "Something went wrong.";
                    }
                }
                
            }
        }
    ?>
    </div> <!--/page-->
    <?php pagefooter(); ?>
</body>
</html>