<?php
    include 'connect.php';
    include 'standardAssets.php';
    include 'check_injection.php';
    $link = connect();
    
    $sql = "SELECT m.MachineID, m.MachineModel, l.LabID, l.LabName, l.Country, l.City, l.LabAddress 
    FROM qPCRmachine AS m 
    LEFT JOIN Laboratory AS l ON m.MachineLab = l.LabID
    ";
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $clean = check_injection($link, $_POST);
        if(!empty($_POST["search"])){
            $result = mysqli_query($link,$sql." WHERE LabID LIKE '%".$clean["search"]."%'");
            if (mysqli_num_rows($result) == 0) {
                dconnect($link);
                header('Location: form_machine.php?error=noresults');
                exit;
            }
        } else {
            $result = mysqli_query($link,$sql);
        }
    } else {
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
    <?php pageheader(); ?>
    <div class="body"><!--Content-->

        <!-- form -->
        <form action="add_machine.php" method="POST" style="border:1px solid #ccc">
        <h2>Add Freezer </h2>
            <label for="model">Machine Model:</label>
            <input type="number" placeholder=" Machine Model" name="model"><br>
            <input type="submit">
        </form>
        <br>

        <!-- search-->
        <div>
            <h>Search Machine by LabID:</h>
            <form action='form_machine.php' method='POST'>
                <input type='text' name='search'/>
                <input type='submit' value='Search'/>
            </form>

            <table border='1'>
                <tr>
                    <th>Machine ID</th>
                    <th>Model ID</th>
                    <th>LabID</th>
                    <th>Lab Name</th>
                    <th>Location</th>
                    <th>Adress</th>
                </tr>    
                <?php
        
                    while($row = mysqli_fetch_row($result)){
                        echo "<tr>
                        <td>".$row[0]."</td>
                        <td>".$row[1]."</td>
                        <td>".$row[2]."</td>
                        <td>".$row[3]."</td>
                        <td>".$row[5].' ('.$row[4].")</td>
                        <td>".$row[6]."</td>
                        </tr>";
                    }
        
                ?>
            </table>
        </div>
    </div>


</form>
    <?php pagefooter();?>
</body>
</html>