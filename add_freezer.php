<?php
   include 'connect.php';
   include 'check_injection.php';

   // User
   session_start();
   if (empty($_SESSION['UserID'])) {
      header('Location: login.php?error=login');
      exit;
   }

   if ($_SERVER['REQUEST_METHOD'] == 'POST') {  
      $link = connect();
      $clean = check_injection($link, $_POST);

      if (empty($_POST["model"])) {
         dconnect($link);
         header("Location: form_freezer.php?error=invalid&item=model");
         exit;
      } else {
         $model= $_POST["model"];
      }

      if (empty($_SESSION['UserLabID'])) { // replace with session lab id
         dconnect($link);
         header("Location: index.php?error=laboratory");
         exit;
      }else {
      $LabID = $_SESSION['UserLabID'];
      }

      $sql = "INSERT INTO `freezer` (`FModel`, `FLabID`) VALUES ('$model', $LabID)";
      if (mysqli_query($link, $sql)) {
         dconnect($link);
         header("Location: form_freezer.php?success=added&item=Freezer");
         exit;
      } else {
         dconnect($link);
         header("Location: form_freezer.php?mysqli_error($link)");
         exit;
      }
   } else {
      dconnect($link);
      header("Location: form_freezer.php");
      exit;
   }
?>