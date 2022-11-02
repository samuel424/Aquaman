<?php
    include 'connect.php';
    $link = connect();
    if(!$resultQPCR = mysqli_query($link,"SELECT * FROM `qPCRrun` WHERE qPCRSuccess IS NULL")){
        echo "Failed to query database for qPCRID options";
    }
    if(!$resultuser = mysqli_query($link,"SELECT * FROM `Account`")){
        echo "Failed to query database for user options";
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
    <h1>Process text file to retrieve qPCR data</h1>
        <p> Please fill in this form below in order to retrieve qPCR information.</p>
        <form action="readct.php" method="post">
        <p> Please enter a .txt file. </p>
            <input type="file" name="textfile"><br>
            <p> Please choose the qPCRrunID that corresponds to the text file. </p>
            <label for='qPCRID'><b>qPCRrunID:</b></label>
                <select name='qPCRrunID' id='qPCRrunID'>
                    <?php
                        // Echo qPCRrunID options for the drop down list
                        while($row = mysqli_fetch_assoc($resultQPCR)) {
                            $catid = $row["qPCRrunID"];
                            $catname = $row["qPCRrunID"];
                            echo "<option value='$catid'>$catname</option>"; 
                        }
                    ?>
                </select><br>
            <p> Please select the one responsible for the curve analysis. </p>
            <label for='CurveAnalys'><b>Responsible:</b></label>
                <select name='CurveAnalys' id='CurveAnalys'>
                    <?php
                        // Echo responsible options for the drop down list
                        while($row = mysqli_fetch_assoc($resultuser)) {
                            $catid = $row["UserID"];
                            $catname = $row["Firstname"].' '.$row["Lastname"];
                            echo "<option value='$catid'>$catname</option>"; 
                        }
                    ?>
                </select><br>
            <p> Please enter the well positions of those who did not meet the requirements. Separate the well positions with commas. </p>
            <label for="CurveEval"><b>Well positions:</b></label>
            <input type="text" name="CurveEval"><br>
            <p> Please fill in if the qPCR was a success or not. Yes means success, no means failure. </p>
            <input type='radio' name='successornot' value='yes' id='sucess'/>
            <label for='success'>yes</label>
            <input type='radio' name='successornot' value='no' id='failure'/>
            <label for='failure'>no</label><br>
            <input type="submit" value="submit">
        </form>
    </div> <!--/page-->
    <?php pagefooter();?>
</body>
</html>