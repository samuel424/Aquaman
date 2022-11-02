<?php
    include 'connect.php'; 
    include "standardAssets.php"; 
    $link = connect();

    $lab = $_SESSION['UserLab'];

    if (isset($_SESSION['UserLabID'])) {
        $labid = $_SESSION['UserLabID'];
        $sql = "SELECT acc.Firstname, acc.Lastname, lab.UserID, lab.LabRole
                FROM labaffiliation as lab
                LEFT JOIN account as acc ON lab.UserID = acc.UserID
                WHERE lab.LabID = $labid ORDER BY lab.LabRole DESC, acc.Firstname ASC";
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
    <?php 
    pageheader();
    ?>
    <!--Content-->
    <div class="body">
        
        <?php
        echo "<b>Laboratory:</b> $lab <br>";
        // show collaborators

        echo "<b>Collaborators: </b><br>";
        
        if (isset($_SESSION['UserLabID'])) {
            while($row = mysqli_fetch_row($result)){
                if ($row[3] == 1){
                    echo $row[0] . ' ' . $row[1] . ', ' . $row[2] . ' (admin)<br>';
                } else {
                    echo $row[0] . ' ' . $row[1] . ', ' . $row[2] . '<br>';
                }
            }
        }

        if ($_SESSION['LabAdmin'] == 1) {
            // A form to add new users to laboratory
            echo '<br><b>Add collaborator:</b><br>
                    <form action="add_affiliation.php" method="post">';
            // User
            $resU = mysqli_query($link,"SELECT * FROM Account");
            if(!$resU){
                echo "Failed to query database for UserID options";
            } else {
                
                echo '<label for="user">User:</label><select name="user">';
                while($row = mysqli_fetch_assoc($resU)) {
                    $catid = $row["UserID"];
                    $catname = $row["Firstname"].' '.$row["Lastname"];
                    echo "<option value='$catid'>$catid: $catname</option>"; 
                }
                echo '</select><br>';
            }
            echo '<label for="role">Admin</label>
                    <input type="checkbox" name="role"><br>
                    <input type="submit" value="Send Invite">
                </form>';
        }
        ?>
        <div>
            <hr/>
            <!-- Manage laboratories -->
            <b>Change laboratory:</b>
            <form action="change_view.php" method='post'>
                <?php
                    // lists all laboratories the user is a member of
                    $userid = $_SESSION['UserID'];
                    $resL = mysqli_query($link,"SELECT lab.LabID, lab.LabName, lab.Country, lab.City 
                                                FROM labaffiliation as aff 
                                                LEFT JOIN laboratory as lab ON aff.LabID = lab.LabID
                                                WHERE aff.UserID = $userid");
                    if(!$resL){
                        echo "Failed to query database for UserID options";
                    } else {
                        echo '<select name="lab">';
                        while($row = mysqli_fetch_assoc($resL)) {
                            $catid = $row["LabID"];
                            $catname = $row["LabName"].', '.$row["Country"].' '.$row["City"];
                            echo "<option value='$catid'>$catname</option>";
                        }
                        echo '</select>';
                    }
                ?>
                <input type="submit" value="Change">
            </form>

            <b>Or create a new one:</b><br>
            <form action='form_laboratory.php' method='post'>
                <input type='submit' value='Create new laboratory'>
            </form>
        </div>
    </div> <!--/page-->
    <?php 
    pagefooter();
    dconnect($link);
    ?>
</body>
</html>
