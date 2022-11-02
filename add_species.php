<?php
include 'connect.php'; //database connection php script

// Login check
session_start();
if (empty($_SESSION['UserID'])) {
   header('Location: login.php?error=login');
   exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') { //coming from register form
   $link=connect();
   if (empty($_POST["Latin"])) {
      dconnect($link);
      header("Location: form_species.php?error=invalid&item=Latin name");
      exit;
   } elseif (strlen($_POST["Latin"]) > 50) {
      dconnect($link);
      header("Location: form_species.php?error=long&item=Latin name&max=50");
      exit;
   } else {
      $Latin= $_POST["Latin"];
   }
   if (empty($_POST["English"])) {
      dconnect($link);
      header("Location: form_species.php?error=invalid&item=English name");
      exit;
   } elseif (strlen($_POST["English"]) > 50) {
      dconnect($link);
      header("Location: form_species.php?error=long&item=English name&max=50");
      exit;
   } else {
      $English= $_POST["English"];
   }
   // Insert
   $sql = "INSERT INTO `Species` (`SpeciesLatin`, `SpeciesEnglish`) VALUES ('$Latin', '$English')";
   if (mysqli_query($link, $sql)) {
      header("Location: form_species.php?success=added&item=Species");
   } else {
      header("Location: form_species.php?error=sql");
   }
}  else {
   header("Location: form_species.php");
}


?>