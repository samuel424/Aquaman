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
        include "standardAssets.php"; pageheader(); 
    ?>
    <!--Content-->
    <div class="page">
        <h1>Contact us:</h1>
        <form action="contact.php" method="post">
            <?php
            if (empty($_SESSION['UserEmail'])) {
                print "<label for='Name'>Name:</label><br>
                <input type='text' name='Name' required></input><br>
                <label for='Email'>Email:</label><br>
                <input type='text' name='Email' required></input><br>";
            } else {
                $name = $_SESSION['UserName'];
                $email = $_SESSION['UserEmail'];
                print "<label for='Name'>Name:</label><br>
                <input type='text' name='Name' value=$name required></input><br>
                <label for='Email'>Email:</label><br>
                <input type='text' name='Email' value=$email required></input><br>";
            }
            ?>
            <label for='Topic'>Topic:</label><br>
            <input type="text" name="Topic" required></input><br>
            <label for='Message'>Message:</label><br>
            <textarea name="Message" cols='64' rows='10' wrap='hard' required></textarea><br>
                        

            <br><input type="submit" name="Send Form">
        </form> 
    </div> <!--/page-->
    <?php pagefooter(); 
    //
    ?>
</body>
</html>