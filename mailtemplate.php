<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
// Load mailserver
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// prepare a link
$maillink = 'https://tinyurl.com/3wcyvb7x';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

//Server settings
$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
$mail->isSMTP();                                            //Send using SMTP
$mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
$mail->Username   = 'aquaman.uu@gmail.com';                     //SMTP username
$mail->Password   = 'IMSproject';                               //SMTP password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
$mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

//Content
$mail->isHTML(true);                                  //Set email format to HTML
$mail->Subject = 'Confirm Your Åquamän Account';
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
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

?>