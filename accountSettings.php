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
    include 'connect.php';
    $link = connect();
    ?>
    <!--Content-->
    <div class="body">
        <?php
            // splice the name
            $name = explode(' ', $_SESSION['UserName']);
            $fname = array_slice($name, 0, -1);
            $lname = array_slice($name, -1);
            $fname_str = '';
            foreach ($fname as $name) {
                $fname_str = $fname_str . ' ' . $name;
            }
            
            // show or change info?
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // change personal information
                echo "<form action='changingSettings.php' method='post'>
                        <label for='fname'><b>First name:</b></label>
                        <input type='text' name='fname' value= $fname_str ><br>
                        <label for='lname'><b>Last name:</b></label>
                        <input type='text' name='lname' value= $lname[0] ><br>
                        <label for='email'><b>Email adress:</b></label>
                        <input type='text' name='email' value=" . $_SESSION['UserEmail'] . "></input><br>
                        <input type='submit' value='Save' name='data_submit'>
                      </form>";
            } else {
                // show personal information
                echo "<b>First name: </b> $fname_str";
                echo "<br><b>Last name: </b> $lname[0]";
                echo "<br><b>Email adress: </b>" . $_SESSION['UserEmail'];
                echo "<form action='accountSettings.php' method='post'>
                        <input type='submit' value='Change'>
                      </form>";
            }
        ?>
        <hr/>
        
        <!-- Change password -->
        <b>Change password:</b><br>
        <form action='changingSettings.php' method='post'>
            <label for='curr_pwd'>Current password:</label>
            <input type='password' name='curr_pwd' required><br>
            <label for='new_pwd'>New password:</label>
            <input type='password' name='new_pwd' required><br>
            <label for='conf_pwd'>Confirm new password:</label>
            <input type='password' name='conf_pwd' required><br>
            <input type='submit' value='Save' name='pwd_submit'>
        </form>
        <hr/>

        <!-- Delete account -->
        <b>Delete account:</b><br>
        To delete your account, type "CONFIRMDELETE" in the field down below.<br> 
        <b style='color:Crimson'>WARNING:</b> Deleting your account is <b>permanent</b> and 
        cannot be reversed!
        <form action='loggingout.php' method='post'>
            <input type='text' name='deltext' required><br>
            <input type='submit' value='Delete Account'>
        </form>
    </div> <!--/page-->
    <?php pagefooter();?>
</body>
</html>