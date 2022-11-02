<?php
    include "connect.php";
    include "standardAssets.php"; 
    include 'check_injection.php';
    $link = connect();
    $sql = "SELECT * FROM Catchmethod";

    if(isset($_POST["search"])){
        $clean = check_injection($link, $_POST);
        $result = mysqli_query($link, $sql ." WHERE CatchmethodName LIKE '%".$clean["search"]."%'");
        
        if (mysqli_num_rows($result) == 0) {
            dconnect($link);
            header('Location: form_catchmethod.php?error=noresults');
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
        //If not loged in header('Location: index.php')
        pageheader();
    ?>
    <!--Content-->
    <div class="body">
        <h2>Add a new standard method of catching fish</h2>
        <form action="add_catchmethod.php" method="post">
            <input type="text" Name = "Method" maxlength='50' required> Catch Method</input><br>
            <input type="submit" value="Add method">
        </form>

        <h2>Catchmethods in database</h2>
        <div>
        <h>Search Location by Name:</h>
        <form action='form_catchmethod.php' method='POST'>
            <input type='text' name='search'/>
            <input type='submit' value='Search'/>
        </form>
    </div>
        <table border='1'>
        <tr><th>ID</th><th>Method</th></tr>
        <?php
            while ($row = mysqli_fetch_row($result)){
                echo "<tr>";
                echo "<td>".$row[0]."</td>";
                echo "<td>".$row[1]."</td>";
                echo "</tr>";
            }  
        ?>
        </table>
    </div> <!--/page-->
    <?php pagefooter();?>
</body>
</html>