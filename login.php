<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Åquamän</title>
    <link rel="stylesheet"  type="text/css" href="css/main.css?ts=<?=time()?>">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v5.15.4/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
       
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Rancho&effect=fire-animation">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
            <link href='https://fonts.googleapis.com/css?family=Vampiro One' rel='stylesheet'>
    </head>
 
<style>

h1 {
    font-family: Trirong;
  font-size: 4.375rem;
  text-align: center;
}

h2 {
    font-family: Trirong;
    color: #020201;
    animation: neon1 1.5s ease-in-out infinite alternate;

}
h3 {
    font-family: Trirong;
      color: #020201;
    animation: neon1 1.5s ease-in-out infinite alternate;

}

hsmall {
  font-family: "Trirong", serif;
  font-size: 1.25rem;

}
h3 {
  font-family: "Trirong", serif;
  color: #020201;
    animation: neon1 1.5s ease-in-out infinite alternate;

}

</style>

<body>
    <?php
    //If not loged in header('Location: index.php')
    include 'connect.php';
    include "standardAssets.php"; pageheader();
    $link = connect();
    if(!$result_user = mysqli_query($link,"SELECT * FROM `Account`")){
        echo "Failed to query database for UserID options";
    }
?>

<div class="container headertext" >
  <div class="row "  >
  <div class="col-sm-8"  >
  <div class="rowpad  "  style = "padding-right:12rem  " >
  <h3>About Åquamän</h3>
      <p>To monitor the well-being of fish, the Åquamän group developed a laboratory information management system (LIMS) tool for mapping the extent of fish stress in order to investigate causative agents and populations at risk and implement the most effective preventative measures. To measure the stress, the expression of stress related genes is investigated in fish tissues. Different species should be compared in different locations and their gene expression should be correlated to stressors, e.g. pH, oxygen content, and heavy metal levels in the water. Researchers can, once registered, enter data from different stages of the study into the database, as well as access information entered by other users, using the user interface. The system should also be able to provide simple analysis tools for researchers to evaluate their data . Throughout the process, a heavy focus lies on traceability of the samples, instruments, reagents, and people involved in the processing of each sample. As an output, several statistical analysis tools are available to correlate stress gene expression levels to the environmental parameters and sampling locations. This way, the LIMS can contribute to gather more comparative datasets between different research projects, and connect and relate fish data from different regions. The output shall also be displayed in a map, such that regional trends can be evaluated.
</p>
    
  </div>
</div>

<div class="col-sm-4 text-primary" >
    <div class="container rowpad form-control" style= "border:2px solid #020201; background-color:#c06442; rounded; box-shadow: 2px 4px 16px 2px #000000;" >
    <div class="container " >
    <div class="col-sm-12 text-white " >
      <h3>Log in</h3>        
      <div class="page">

      <form action="loggingin.php" method="post">
            <input type="text" placeholder="Emailadress" name="Emailaddress" required ></input><br>
            <input type="password" placeholder="Password" name="Password" required></input><br>

            <!--Captcha-->
            <div class="elem-group">
                <label for="captcha"></label><br>
                <img src="captcha/captcha.php" alt="CAPTCHA" class="captcha-image"><i class="fas fa-redo refresh-captcha"></i>
                <!-- idk what class does -->
                <br><br>
                <input type="text" id="captcha"  placeholder="Enter CAPTCHA" name="captcha_challenge" pattern="[A-Z]{6}">
            </div>
            <br>
            <input type="submit" name="LogIn">
        </form>  
        </div> 
    </div>     
    </div> 
    </div> 
    </div>

</div>
</div>

  <div class="row samcontainer" >
  <div class="col-sm-8 ">
  <div class="row" style = "padding-left:12rem  ">
  
<!--cointer text-->

    </div></div></div>
    <!--/page-->
    </div> <br>

    
    <div class="row" style="background-color:#FFFFFF">
    
    <div class="col-sm-12" style="background-color:#FFFFFF">

    </div>  
</main>

</div>
</form>
    <?php pagefooter();?>
</body>
</html>