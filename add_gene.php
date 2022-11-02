<?php
    include 'check_injection.php';
    include 'connect.php';

    // Login check
    session_start();
    if (empty($_SESSION['UserID'])) {
        header('Location: login.php?error=login');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') { //coming from register form
        $link = connect();
        $clean = check_injection($link,$_POST);
        
        //Genename
        if (empty($clean["name"])) {
            dconnect($link);
            header ('Location: form_gene.php?error=invalid&item=Genename');
            exit;
        } elseif (strlen($clean["name"]) > 30) {
            dconnect($link);
            header ('Location: form_gene.php?error=long&item=Genename&max=30');
            exit;    
        } else {
            $name = $clean["name"];
        }
        
        // Species
        if (empty($clean['Species'])) {
            dconnect($link);
            ('Location: form_gene.php?error=invalid&item=Species');
            exit;
        }else {
            $Species = $clean['Species'];
        }

        $check_sql = "SELECT * FROM Gene WHERE GName = '$name' AND TargetSpecies = '$Species'";
        $check_res = mysqli_query($link, $check_sql);
        if (($check_res->num_rows) > 0) { //gene exists for species
            dconnect($link);
            header("Location: form_gene.php?error=exists&item=Gene for species");
            exit;
        }

        // Houskeeping
        if(!empty($clean["housekeeping"])) {
            $housek = 1;
        } else {
            $housek = 0;
        }
        
        // Insert
        $sql = "INSERT INTO Gene (GName, TargetSpecies, Housekeeping) VALUES ('$name', $Species, $housek)";

        if (mysqli_query($link,$sql)){
            dconnect($link);
            header ('Location: form_gene.php?success=added&item=Gene');
            exit;
        }else {
            dconnect($link);
            header ('Location: form_gene.php?error=sql');
            exit;
        }
    } else {
        dconnect($link);
        header ('Location: form_gene.php');
        exit;
    }
?>