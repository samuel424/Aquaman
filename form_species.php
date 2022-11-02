<?php 
include 'connect.php';
include "standardAssets.php";
include 'check_injection.php';
$link=connect();

$sql = "SELECT * FROM Species";

if(isset($_POST["search"])){
    $clean = check_injection($link, $_POST);
    $result = mysqli_query($link, $sql." WHERE SpeciesLatin LIKE '%".$clean["search"]."%'");
    
    if (mysqli_num_rows($result) == 0) {
        dconnect($link);
        header('Location: form_species.php?error=noresults');
        exit;
    }
}else {
    $result = mysqli_query($link,$sql);
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
 
        <form action="add_species.php" method="POST" style="border:1px solid #ccc">
        <div>
            <h2>Species </h2>
            <p>Please fill in this form below to add species:</p>
            <label><b>Name in Latin:</b></label>
            <input type="text" placeholder="Enter Latin Name" name="Latin" maxlength="50" required><br><br>
            <label><b> Name in English:</b></label>
            <input type="text" placeholder="Enter English Name" name="English" maxlength="50" required><br><br>
            
            <div>
                <button name="submit" type="submit">Add Species</button>
            </div>
        </form>
        <br>
        </div>

        <div>
            <p>Search Species in Latin:</p>
            <form action='form_species.php' method='POST'>
                <input type='text' name='search'/>
                <input type='submit' value='submit'/>
            </form>

            <table border='1'>
                <tr>
                    <th>Name in Latin</th>
                    <th>Name in Enlgish</th>
                </tr>
            
            <?php
                while($row = mysqli_fetch_row($result)){
                echo "<tr><td>";
                echo $row[1];
                echo "</td><td>";
                echo $row[2];
                echo "</td></tr>";
                }
                dconnect($link);
            ?>
            </table>
        </div>
    </div> <!--Body-->
    </div>
    <?php pagefooter();?>
</body>
</html>
