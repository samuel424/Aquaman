<?php
    include 'connect.php';
    $link = connect();
    if(!$resultleader = mysqli_query($link,"SELECT * FROM `Account`")){
        echo "Failed to query database for UserID options";
    }
    if(!$resultlocation = mysqli_query($link, "SELECT * FROM `SamplingLocation`")){
        echo "Failed to query database for LocationID options";
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
    <?php include "standardAssets.php"; pageheader();?>
    <!--Content-->
    <div class="body">
    <h2>Add Laboratory</h2>
        <form action="add_laboratory.php" method="POST">
            <label for="LabName">Laboratory Name:</label>
            <input type="text" name="LabName" maxlength="30" require><br>                   
            <label for="Country">Country:</label>
            <input type="text" name="Country" maxlength="40"><br>
            <label for="City">City:</label>
            <input type="text" name="City" maxlength="40"><br>
            <label for="LabAddress">Laboratory Address:</label>
            <input type="text" name="LabAddress" maxlength="100"><br>
            <?php
            echo "<input type='hidden' name='MainAccount' value='".$_SESSION['UserID']."'>"
            ?>
            <!--<label for='MainAccount'>Main Account:</label>
                <select name='MainAccount' id='MainAccount' require>
                    <?php
                        // Echo leader options for the drop down list
                        /*
                        while($row = mysqli_fetch_assoc($resultleader)) {
                            $catid = $row["UserID"];
                            $catname = $row["Firstname"].' '.$row["Lastname"];
                            echo "<option value='$catid'>$catname</option>"; 
                        }*/
                    ?>
                </select><br>-->
            <input type="submit" value="Submit">
        </form>

        <?php
        include 'check_injection.php';
        //Connect to DB for search
        $link = connect();
        $sql = "SELECT * FROM Laboratory";
        
        if(isset($_POST["search"])){
            $clean = check_injection($link, $_POST);
            $search= $clean["search"]; 
            $result = mysqli_query($link, $sql ."WHERE Labname LIKE '$search'");
            
            if (mysqli_num_rows($result) == 0) {
                sconnect($link);
                header('Location: form_location.php?error=noresults');
                exit;
            }
        }else {
            $result = mysqli_query($link,$sql);
        }

        echo "<table border='1'>"; 
        echo "<tr>
            <th>ID</th>
            <th>Lab Name</th>
            <th>Country</th>
            <th>City</th>
            <th>LabAdress</th>
        </tr>";
        while($row = mysqli_fetch_row($result)){
            echo "<tr>
            <td>".$row[0]."</td>"
            ."<td>".$row[1]."</td>"
            ."<td>".$row[2]."</td>"
            ."<td>".$row[3]."</td>"
            ."<td>".$row[4]."</td>"
            ."</tr>";
        }
        echo "</table>";

        dconnect($link);
        ?>
    </div> <!--/page-->
    <?php pagefooter();?>
</body>
</html>