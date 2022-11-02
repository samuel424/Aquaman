<?php
    include 'connect.php';
    include "standardAssets.php";
    $link = connect();
    // if(!$resultleader = mysqli_query($link,"SELECT * FROM `Account`")){
    //     echo "Failed to query database for UserID options";
    // }
    if(!empty($_POST) or isset($_GET['link'])){
        if (isset($_POST['all'])){
            $where = "";
    } elseif(isset($_POST['search']) or isset($_GET['link'])){
        if (isset($_POST['search'])){
            $clean = check_injection($link, $_POST);
            $search = trim($clean['search'], ',');
        } elseif (isset($_GET['link'])){
            $search = trim($_GET['link'], ',');
        }
        $samplings = explode(',', $search);
        $where = 'WHERE SamplingID = '.$search[0];
        if (count($samplings) > 1){
            unset($samplings[0]);
            foreach($samplings as $id){
                $where = $where.' OR SamplingID = '.$id;
            }
        }
    }
    $sql = "SELECT SamplingID, LeaderID, LocationName, STimestamp 
    FROM fieldsampling AS f 
    LEFT JOIN samplinglocation AS s 
    ON f.LocationID = s.LocationID ".$where;

    $searchresult = mysqli_query($link, $sql);
    if(!$searchresult){
        echo mysqli_error($link);
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
    <?php pageheader();?>
    <div class="body">
        <div>
            <h3>Field sampling </h3>
            <form action="add_rawmaterial.php" method="post">
                    
                
                    <?php 
                    inputLocation($link);
                    inputDate();
                    ?>

                    <label for="ph">pH:</label>
                    <input type="number" id="ph" name="ph"><br>
                    <label for="oxygen">Oxygen:</label>
                    <input type="number" step="0.0001" id="oxygen" name="oxygen"><br>
                    <label for="hg">Hg:</label>
                    <input type="number" step="0.0001" id="hg" name="hg"><br>
                    <label for="pb">Pb:</label>
                    <input type="number" step="0.0001" id ="pb" name="pb"><br>
                    <input type="submit" value="Submit">
            </form>

        </div>
        <div>
            <h3>Catchmethods in database</h3>
    
            <h>Search Sampling by ID:</h>
            <form action='form_rawmaterial.php' method='POST'>
                <input type='text' name='search'/>
                <input type='submit' value='Search'/>
                <input type='submit' name = 'all' value='Show all samplings'>
            </form>
        
            <table border='1'>
            <tr><th>Sampling ID</th><th>Expedition leader ID</th><th>Location</th><th>Time</th></tr>
            <?php
            if (!empty($_POST) or isset($_GET['link'])){
                while ($row = mysqli_fetch_row($searchresult)){
                    echo "<tr>";
                    echo "<td>".$row[0]."</td>";
                    echo "<td>".$row[1]."</td>";
                    echo "<td>".$row[2]."</td>";
                    echo "<td>".$row[3]."</td>";
                    echo "</tr>";
                } 
            }
            ?>
            </table>
        </div>
    </div> /page




    <?php pagefooter();?>


</body>

</html>