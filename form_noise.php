<?php
    include "standardAssets.php"; 
    include "connect.php";
    include 'check_injection.php';
    $link = connect();

    $sql = "SELECT l.LocationID, l.LocationName, 
    n.NDate, n.NoiseRank, 
    u.Firstname, u.Lastname
    FROM Noise AS n
    LEFT JOIN SamplingLocation AS l ON n.LocationID = l.LocationID
    LEFT JOIN Account AS u ON n.ReporterID = u.UserID
    ";
    
    if(isset($_POST["search"])){
        $clean = check_injection($link, $_POST);
        $result_ = mysqli_query($link, $sql ." WHERE l.LocationID LIKE '%".$clean["search"]."%'");
        
        if (mysqli_num_rows($result_) == 0) {
            dconnect($link);
            header('Location: form_location.php?error=noresults');
            exit;
        }
    }else {
        $result_ = mysqli_query($link,$sql);
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
        <div>
            <h2>Make a noise report</h2>
            <form action="add_noisereport.php" method="post">
                <?php
                inputLocation($link);
                inputDate(False);
                ?>
            <!-- rank -->
            <label for="rank">Rank:</label>
            <input type="number" name="rank" placeholder='0 - 10' min="0" max="10" require><br>
            
            <input type="submit" value="Submit"><br>
        </form>
        </div>

        <div>
            <h>Search Fish by location ID:</h>
            <form action='form_noise.php' method='POST'>
                <input type='text' name='search'>
                <input type='submit' value='Search'>
            </form>
            <table border='1'> 
                <tr>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Reporter</th>
                    <th>Noise rank</th>
                </tr>
            
                <?php
                    while ($row = mysqli_fetch_row($result_)) {
                        echo "<tr>
                            <td>".$row[0].": ".$row[1]."</td>
                            <td>".$row[2]."</td>
                            <td>".$row[4]." ".$row[5]."</td>
                            <td>".$row[3]."</td>
                        </tr>";
                    }
                ?>
            </table>
        </div>
    </div> <!--/page-->
    <?php pagefooter();?>
</body>
</html>
