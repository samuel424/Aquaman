<?php
    session_start();
    include "connect.php";
    include "check_injection.php";
    include "mailtemplate.php";

    // Connect Database
    $link = connect();
    $clean = check_injection($link, $_GET);

    // Emailadress
    if (empty($clean["Emailaddress"])) {
        dconnect($link);
        header("Location: form_contact.php?error=invalid&item=Email address");
        exit;
    } elseif (strlen($clean["Emailaddress"]) > 320) {
        dconnect($link);
        header("Location: form_contact.php?error=long&item=Email address&max=320");
        exit;
    } else {
        $email = $clean["Emailaddress"];
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    }
    // send conformation email
    $mail->Subject = $_POST['Topic'];
    $mail->Body    = $_POST['Message'] . '<br><br>' . $_POST['Name'] . '<br>' . $email;
    $mail->addAddress('aquaman.uu@gmail.com');
    try {
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
    dconnect($link);
    header("Location: form_contact.php?success=added&item=Ticket");
    exit;
?>