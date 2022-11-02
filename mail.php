
   <?php
    session_start();
    include "connect.php";
    include "check_injection.php";
    include "mailtemplate.php";

    // Connect Database
    $link = connect();
    $clean = check_injection($link, $_GET);

    if(isset( $_POST['name']))
    $name = $_POST['name'];
    if(isset( $_POST['email']))
    $email = $_POST['email'];
    if(isset( $_POST['message']))
    $message = $_POST['message'];
    if(isset( $_POST['subject']))
    $subject = $_POST['subject'];
    
  
    $mail->Body    = $message . '<br><br>' .$name . '<br>' . $email;
    $mail->addAddress('aquaman.uu@gmail.com');
    try {
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
    dconnect($link);
    header("Location: index.php?success=added&item=Ticket");
    exit;
?>
