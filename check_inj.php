<?php
include 'connect.php';
include 'check_injection.php';
$link = connect(FALSE);

if($link){
  echo 'good';
}

echo "here<br>";
$cleaned = check_injection($link, $_POST);
print_r($cleaned['id']);
print_r($cleaned['ttt']);
print_r($cleaned['email']);

/*
OLD REGISTER
<?php
    session_start();
    include "connect.php";
    include "check_injection.php";

    // Connect Database
    $link = connect();

    $clean = check_injection($link, $_POST); 
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') { // checks that coming from register form

        // Captcha
        if ($clean['captcha_challenge'] != $_SESSION['captcha_text']){
            echo "not good";
            //header("register.php/?error=captcha");
        } else {
            echo "okay";
        } */
       /*
      // Sanitize and check that all required inputs exist
      if (isset($POST_["Emailadress"])) {
          $email = mysqli_real_escape_string($POST_["FirstName"]);
          $email = filter_var($email, FILTER_SANITIZE_EMAIL)
      } else {
          header("Location: register.php/?error=invalid_email");
      }
      if (isset($POST_["pwd"])) {
          $pwd = mysqli_real_escape_string($POST_["Password"]);
      } else {
          header("Location: register.php/?error=invalid_pwd");
      }
      if (isset($POST_["FirstName")) {
          $fname = mysqli_real_escape_string($POST_["FirstName"]);
      } else {
          header("Location: register.php/?error=invalid_first_name");
      }
      if (isset($POST_["LastName")) {
          $lname = mysqli_real_escape_string($POST_["LastName"]);
      } else {
          header("Location: register.php/?error=invalid_last_name");
      }
      if (isset($POST_["ConfirmPassword"])) {
          $cpwd = mysqli_real_escape_string($POST_["ConfirmPassword"]);
      } else {
          header("Location: register.php/?error=confirm_pwd");
      }

      // Validates Email is correct format
      if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

          // Check if email already has account
          $sql1 = "SELECT UserID FROM Accounts WHERE Email = '$email'";
          $sql2 = "SELECT UserID FROM Temporary WHERE Email = '$email'";

          if (mysql_num_rows(mysql_query($sql1)) > 0) {
              header("Location: register.php/?error=invalid_email");
          } elseif (mysql_num_rows(mysql_query($sql2)) > 0) {
              header("Location: register.php/?error=invalid_email");
          }else {
              
              // Check password == conformPassword
              if ($pwd == $cpwd) {

                  // Check password complex enought
  
                  //Generate salt
                  $salt = 0;

                  // hash password    
                  $pwd = $pwd;
  
                  // make confirm hash
                  $hash = '';
  
                  // store in tempoary table
                  $sql='';

                  dconnect($link);
                  header("Location:index.php/?reg_res=confirm_sent&email=$email&hash=$hash"); // should instead send link in email
                  
              } else {
                  dconnect($link);
                  header("Location: register.php/?error=confirm_pwd");
              }
          }
      } else {
          dconnect($link);
          header("Location: register.php/?error=invalid_email");
      }
  
  } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') { // confirmation link
          
          // Sanitize and validate email
          $email = mysqli_real_escape_string($_GET["adress"]);
          $email = filter_var($email, FILTER_SANITIZE_EMAIL);
          if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

              // sanitize hash
              $hash = mysqli_real_escape_string($_GET["hash"]);
              
              // testing
              echo $email;

              // Try to fetch email from temporary table
              $sql = "SELECT * FROM Temporary WHERE Email = '$email'";
              $result = mysql_query($link, $sql);
              if (mysql_num_rows($result) = 1) {

                  //$realhash = // from temporary

                  // check the hash
                  //if ($hash == $realhash) {

                      //$fname =
                      //$lname =
                      //$email =
                      //$salt =
                      //$pwd =

                      // add user to Account

                      // remove user from Temporary


                      dconnect($link);
                      // header("Location: login.php");
                  //} else {
                  //    header("Location: register.php);
                  //}
              } //else {
              //    dconnect($link);
              //    header("Location: register.php/?error=invalid_email");
              //}
          }else { // Invalid email
              dconnect($link);
              header("Location: register.php/?error=invalid_email");
          }
  }else { // no form send to form
      dconnect($link);
      header('Location: register.php');
  }
  */
?>
