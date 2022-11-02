<?php
    include 'connect.php';
    include "standardAssets.php"; 
    include 'check_injection.php';
    //Connect to DB for search
    $link = connect();

    //Do the search
    $sql = "SELECT sl.LocationID, sl.LocationName, sl.CorLatitude, sl.CorLongitude, n.NoiseRank, n.NDate, n.User
    FROM SamplingLocation AS sl
    LEFT JOIN (
        SELECT n.LocationID, NDate, n.NoiseRank, a.UserID AS User FROM NOISE AS n
        LEFT JOIN Account AS a ON n.ReporterID = a.UserID
        ORDER BY n.NDate
    ) AS n ON sl.LocationID = n.LocationID";
    if(isset($_POST["search"])){
        $clean = check_injection($link, $_POST);
        $result = mysqli_query($link, $sql ." WHERE LocationName LIKE '%".$clean["search"]."%'");
        
        if (mysqli_num_rows($result) == 0) {
            dconnect($link);
            header('Location: form_location.php?error=noresults');
            exit;
        }
    }else {
        $result = mysqli_query($link,$sql);
    }
    dconnect($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <?php pageheader();?>
    
    <!--Content-->
    <div class="body">

    <!-- the actual form-->
    <form action="add_location.php" method="POST" style="border:1px solid #ccc">
    <div>
        <h2>Add sampling location</h2>
            <label for="loc_name"><b>Location name:</b></label>
            <input type="text" name="loc_name" size = "20" maxlength="30" required><br>
            <label for="cor_lat"><b>Latitude (Y):</b></label>
            <input type="number" step="0.0001" name="cor_lat" size ="10" required><br>
            <label for="cor_long"><b>Longitude (X):</b></label>
            <input type="number" step="0.0001" name="cor_long" size = "10" required><br>
    
            <input type="submit" value="Submit"><br>
        </form><br>
    </div>

    <!-- search-->
    <div>
        <h>Search Location by Name:</h>
        <form action='form_location.php' method='POST'>
            <input type='text' name='search'/>
            <input type='submit' value='Search'/>
        </form>
    </div>
    <div> 
        <table border='1'>
            <tr>
                <th>ID</th>
                <th>Location Name</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Latest Noise</th>
                <th>Date</th>
                <th>Reporter</th>
            </tr>
        <?php
        while($row = mysqli_fetch_row($result)){
            echo "<tr>
            <td>".$row[0]."</td>"
            ."<td>".$row[1]."</td>"
            ."<td>".$row[2]."</td>"
            ."<td>".$row[3]."</td>"
            ."<td>".$row[4]."</td>"
            ."<td>".$row[5]."</td>"
            ."<td>".$row[6]."</td>"
            ."</tr>";
        }
        ?>
        </table>
    </div>
    </div> <!--/page-->
    <?php pagefooter();?>
</body>
</html>
