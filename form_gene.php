<?php 
    include 'connect.php';
    include "standardAssets.php";
    include 'check_injection.php';
    $link = connect();

    // Genes
    $sql = "SELECT g.GeneID, g.GName, s.SpeciesLatin, g.Housekeeping
    FROM Gene AS g LEFT JOIN Species AS s ON g.TargetSpecies = s.SpeciesID";
    if(isset($_POST["search"])){
        $clean = check_injection($link, $_POST);
        $sql = $sql ." WHERE s.SpeciesLatin LIKE '%".$clean["search"]."%'";
        $result_ = mysqli_query($link, $sql);
        
        if (mysqli_num_rows($result_) == 0) {
            dconnect($link);
            header("Location: form_gene.php?error=noresults&test=.$sql");
            exit;
        }
    }else { 
        $result_ = mysqli_query($link,$sql);
    }

    //Probes
    $sql2 = "SELECT p.ProbeID, p.PrType, p.TargetGene FROM Probe AS p";
    $result2 = mysqli_query($link, $sql2);
    $prime_f = [];
    $prime_r = [];
    $probe = [];
    while ($row = mysqli_fetch_row($result2)) {
        if ($row[1] == 'F') {
            if (!isset($prime_f[$row[2]])){
                $prime_f[$row[2]] = [$row[0]];
            } else {
                array_push($prime_f[$row[2]],$row[0]);
            }
        } elseif ($row[1] == 'R') {
            if (!isset($prime_r[$row[2]])){
                $prime_r[$row[2]] = [$row[0]];
            } else {
                array_push($prime_r[$row[2]],$row[0]);
            }
        } else {
            if (!isset($probe[$row[2]])){
                $probe[$row[2]] = [$row[0]];
            } else {
                array_push($probe[$row[2]],$row[0]);
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Åquamän</title>
    <link rel="stylesheet" type="text/css" href="css/main.css?ts=<?=time()?>">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js?ts=<?=time()?>"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?ts=<?=time()?>"></script>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v5.15.4/js/all.js?ts=<?=time()?>" crossorigin="anonymous"></script>
        <!-- Google fonts-->
    
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Trirong">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Rancho&effect=fire-animation">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <link href='https://fonts.googleapis.com/css?family=Vampiro One' rel='stylesheet'>
        
<script src="scroll-down.js"></script>

<style>
h1 {
font-family: Trirong;


h2 {
font-family: Trirong;

}
h3 {
font-family: Trirong;

}

h3 {
font-family: "Trirong", serif;

}


</style>

    </head>
    <body style="font-family: Trirong";>
    <?php 
        pageheader();
    ?>
    <!--Content-->
    <div class="body">
        <h3>Add new gene</h3>
        <form action="add_gene.php" method="post">
            <label for="name"><b>Genename</b></label>
            <input type="text" name="name" size = "20" maxlength="30" required><br>

            <label for='Species'>Species:</label>
            <?php
                if(!$result = mysqli_query($link, "SELECT * FROM `species`")){
                    echo "Error: Problem connecting server";
                } else {
                    echo "<select name='Species' required>";
                    while($row = mysqli_fetch_assoc($result)) {
                    $SpeciesID = $row["SpeciesID"];
                    $latin = $row["SpeciesLatin"];
                    echo "<option value='$SpeciesID'>$latin</option>"; 
                    }
                    echo "</select>";
                }
            ?>
            <br>

            <label for="housekeeping"><b>Houskeeping</b></label>
            <input type="checkbox" name="housekeeping"><br>

            <input type="submit" value="Add Gene">
        </form>

        <!-- search-->
    <div>
        <h>Search: Gene by species name</h>
        <form action='form_gene.php' method='POST'>
            <input type='text' name='search'/>
            <input type='submit' value='Search'/>
        </form>
    </div>
    <div> 
        <table border='1'>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Species</th>
                <th>Housekeeping</th>
                <th>Forward Primers</th>
                <th>Backward Primers</th>
                <th>Probes</th>
            </tr>
        <?php
        while($row = mysqli_fetch_row($result_)){
            echo "<tr>
            <td>".$row[0]."</td>"
            ."<td>".$row[1]."</td>"
            ."<td>".$row[2]."</td>"
            ."<td>".$row[3]."</td>";
            echo"<td>";
            if (!isset($prime_f[$row[0]])){
                echo '';
            } else {
                foreach ($prime_f[$row[0]] as $p) {
                    echo $p.', ';
                }
            }
            echo"</td><td>";
            if (!isset($prime_r[$row[0]])){
                echo '';
            } else {
                foreach ($prime_r[$row[0]] as $p) {
                    echo $p .', ';
                }
            }     
            echo "</td><td>";
            if (!isset($probe[$row[0]])){
                echo '';
            } else {
                foreach ($probe[$row[0]] as $p) {
                    echo $p .', ';
                }
            }
            echo "</td></tr>";
        }
        ?>
        </table>
    </div>
    </div> <!--/page-->
    <?php
        pagefooter();
        dconnect($link);
    ?>
</body>
</html>