<?php
session_start();
$_SESSION['aqualogin'] = 'wearein';
// set session variables
$_SESSION['UserID'] = 1;
$_SESSION['UserEmail'] = '';
$_SESSION['UserName'] = 'Debug';
$_SESSION['UserRole'] = 'Admin';
$_SESSION['UserLab'] = 'None';
$_SESSION['UserLabID'] = 1;
$_SESSION['LabAdmin'] = 1;
header("Location: index.php?success=login");