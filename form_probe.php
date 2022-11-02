<?php 
    include "standardAssets.php"; 
    include "connect.php";
    include 'check_injection.php';
    $link = connect();

    if(!$result = mysqli_query($link, "SELECT 
    GeneID, GName, s.SpeciesLatin 
    FROM Gene AS g
    LEFT JOIN Species AS s ON g.TargetSpecies = s.SpeciesID 
    ")){
        echo "Error: Problem connecting server";
    }
    
    $sql = "SELECT p.ProbeID, p.ProbeName, p.PrType, p.Fluor, p.ProbeSequence, 
    g.GeneID, g.GName, s.SpeciesLatin 
    FROM Probe AS p
    LEFT JOIN Gene AS g ON p.TargetGene = g.GeneID
    LEFT JOIN Species AS s ON g.TargetSpecies = s.SpeciesID
    ";
    if(isset($_POST["search"])){
        $clean = check_injection($link, $_POST);
        $resultP = mysqli_query($link, $sql." WHERE g.GName LIKE '%".$clean["search"]."%'");
        
        if (mysqli_num_rows($resultP) == 0) {
            dconnect($link);
            header("Location: form_probe.php?error=noresults");
            exit;
        }
    }else { 
        $resultP = mysqli_query($link,$sql);
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
        <form action="add_probe.php" method="post">
            <label for="type" maxlength="1">Type</label>
            <select name="type">
                <option value="F">Forward primer</option>
                <option value="R">Reverse primer</option>
                <option value="P">Probe</option>
            </select>
            <label for="Target">Gene:</label>
            <select name='Target' required>
            <?php   
                while($row = mysqli_fetch_assoc($result)) {
                    $GeneID = $row["GeneID"];
                    $GName = $row["GName"]."(".$row['SpeciesLatin'].')';
                    echo "<option value='$GeneID'>$GName</option>"; 
                }
            ?>
            </select><br>
            <label for="name">Probename</label>
            <input type="text" name="name" maxlength="30" required><br>
            
            
            <label for="type">Fluorphore</label>
            <input type="text" name="fluor" maxlength="10" size='5' ><br>
            <label for="Seq">Sequence</label><br>
            <textarea name="seq" cols="30" rows="10" maxlength="40" required></textarea><br>
            <input type="submit" value="Add probe">
       </form> 

            <!-- search-->
    <div>
        <h>Search: Probe by genename</h>
        <form action='form_probe.php' method='POST'>
            <input type='text' name='search'/>
            <input type='submit' value='Search'/>
        </form>
    </div>
    <div> 
        <table border='1'>
            <tr>
                <th>ID</th>
                <th>Name</th> 
                <th>Type</th>
                <th>Sequence</th>
                <th>Target Gene</th>
                <th>Fluorophore</th> 
            </tr>
        <?php
        while($row = mysqli_fetch_row($resultP)){
            echo "<tr>
            <td>".$row[0]."</td>
            <td>".$row[1]."</td>
            <td>";
            if ($row[2] == 'F') {
                echo "Forward primer";
            } elseif ($row[2] == 'R') {
                echo "Reverse primer";
            } elseif ($row[2] == 'P') {
                echo "Probe";
            }
            echo"</td>
            <td>".$row[4]."</td>
            <td>".$row[5].': '.$row[6].'('.$row[7].")</td>
            <td>".$row[3]."</td>
            </tr>";
        }
        ?>
        </table>
    </div>


    </div> <!--/page-->
    <?php 
    dconnect($link);
    pagefooter();
    ?>
</body>
</html>