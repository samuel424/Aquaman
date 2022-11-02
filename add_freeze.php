<?php
include 'connect.php'; //database connection php script

// User
session_start();
if (empty($_SESSION['UserID'])) {
   header('Location: login.php?error=login');
   exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {  
   $link=connect();

   if (empty($_POST["FreezerID"])) {
      dconnect($link);
      header('Location: form_freeze.php?error=invalid&item=Freezer');
      exit;
   } else {
      $FreezerID= $_POST["FreezerID"];
   }

   if (empty($_POST["Temp"])) {
      dconnect($link);
      header('Location: form_freeze.php?error=invalid&item=Temperature');
      exit;
   } else {
      $Temp= $_POST["Temp"];
   }

$TimeIn= $_POST["TimeIn"];
$TimeOut= $_POST["TimeOut"];

   if (empty($_POST["FrType"])) {
      dconnect($link);
      header('Location: form_freeze.php?error=invalid&item=Type');
      exit;
   } else {
      $FrType= $_POST["FrType "];

      if ($FrType == 0) {
         if (empty($_POST["FrFishID"])) {
            dconnect($link);
            header('Location: form_freeze.php?error=invalid&item=Fish');
            exit;
         }
         $FrFishID= $_POST["FrFishID"];
         $FrSample= 0;
         $FrRNA= 0;
      } elseif ($FrType == 1) {
         if (empty($_POST["FrSample"])) {
            dconnect($link);
            header('Location: form_freeze.php?error=invalid&item=Tissue sample');
            exit;
         }
         $FrFishID= 0;
         $FrSample= $_POST["FrSample"];
         $FrRNA= 0;
      } else {
         if (empty($_POST["FrRNA"])) {
            dconnect($link);
            header('Location: form_freeze.php?error=invalid&item=RNA sample');
            exit;
         }
         $FrFishID= 0;
         $FrSample= 0;
         $FrRNA= $_POST["FrRNA"];
      }
   }

$sql = "INSERT INTO `freeze` (`FrFreezer`, `Temperature`,`FrInTime`, `FrOutTime`, `FrType`,`FrFish`,`FrSample`,`FrRNA`) 
VALUES ('$FreezerID', '$Temp', '$TimeIn', '$TimeOut', '$FrType', '$FrFishID', '$FrSample', '$FrRNA')";


if (mysqli_query($link, $sql)) {
    echo "New Freezer has been added successfully!";
    header("Location: form_freeze.php?success='added'&item='Sample to freezer'");
 } else {
    echo "Error: " . $sql . ":-" . mysqli_error($link);
    header("Location: form_freeze.php?error=sql");
 }

//$stmt->close();

} else {
   header("Location: form_freeze.php");
}
?>