@@ -1,222 +0,0 @@
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>�quam�n</title>
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
</head>
 

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

<div class="jumbotron text-center"style="background-color:#FFFFFF">
  <h1>Welcome to �quam�n</h1>
  <p>The LIMS system where you can save your fish</p> 
  <img src="�quam�n.png" class="img-fluid" width="610" height="610"> 
</div>
<div class="p-3 mb-2  text-white" style="background-color:#008190">
<div class="container" >
  <div class="row">

    <div class="col-sm-4" >
      <h3>Our system</h3>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque gravida tempor risus ut faucibus. Donec a metus sollicitudin est posuere hendrerit. Fusce vel nisi maximus, consequat lectus ut, dictum sem. Quisque nec ex sed ex facilisis consectetur. Mauris id sem non dolor elementum tempor at consequat mauris. Aenean imperdiet ipsum nisl, in varius enim fringilla quis. Nullam sollicitudin turpis ex, eu venenatis sem volutpat ac. Cras quam enim, lacinia vel aliquam sit amet, commodo sed ligula. Nullam et aliquam elit, vel euismod ipsum. Suspendisse luctus nec orci eget tristique. Nam pulvinar molestie diam, ut interdum felis pulvinar ut. Duis finibus lacus id risus imperdiet, at suscipit turpis aliquet.

Aliquam viverra lectus odio, vel tempor nulla maximus ut. Cras quis malesuada velit. Aliquam ornare ornare lacus a tincidunt. Etiam ut nibh ultrices, tincidunt turpis ac, luctus diam. Integer finibus, ex sed aliquam efficitur, orci risus ultrices est, at varius augue enim nec dui. Donec facilisis pretium felis eu luctus. Pellentesque dui quam, laoreet vitae cursus vel, laoreet a neque. Phasellus in diam vitae dolor viverra commodo. Integer convallis sodales cursus. Integer et augue nec turpis vestibulum pellentesque. Nunc ac dui sed mi dignissim pellentesque. Nunc condimentum justo vitae lorem mollis, eget tristique risus faucibus. Maecenas sed ullamcorper risus, eu venenatis.</p>
    </div>
    <div class="col-sm-4">
      <h3>Public data</h3>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque gravida tempor risus ut faucibus. Donec a metus sollicitudin est posuere hendrerit. Fusce vel nisi maximus, consequat lectus ut, dictum sem. Quisque nec ex sed ex facilisis consectetur. Mauris id sem non dolor elementum tempor at consequat mauris. Aenean imperdiet ipsum nisl, in varius enim fringilla quis. Nullam sollicitudin turpis ex, eu venenatis sem volutpat ac. Cras quam enim, lacinia vel aliquam sit amet, commodo sed ligula. Nullam et aliquam elit, vel euismod ipsum. Suspendisse luctus nec orci eget tristique. Nam pulvinar molestie diam, ut interdum felis pulvinar ut. Duis finibus lacus id risus imperdiet, at suscipit turpis aliquet.

Aliquam viverra lectus odio, vel tempor nulla maximus ut. Cras quis malesuada velit. Aliquam ornare ornare lacus a tincidunt. Etiam ut nibh ultrices, tincidunt turpis ac, luctus diam. Integer finibus, ex sed aliquam efficitur, orci risus ultrices est, at varius augue enim nec dui. Donec facilisis pretium felis eu luctus. Pellentesque dui quam, laoreet vitae cursus vel, laoreet a neque. Phasellus in diam vitae dolor viverra commodo. Integer convallis sodales cursus. Integer et augue nec turpis vestibulum pellentesque. Nunc ac dui sed mi dignissim pellentesque. Nunc condimentum justo vitae lorem mollis, eget tristique risus faucibus. Maecenas sed ullamcorper risus, eu venenatis.</p>
    </div>

    <div class="col-sm-4 text-primary" >
    <div class="container bg-warning" style= "border:3px solid #cecece;" >
    <div class="container" >
    <div class="col-sm-12 text-white " >
      <h3>Register Today</h3>        
      <div class="page">
       <form action="registeringAccount.php" method="post">
            <input type="text" placeholder="First Name" name="FirstName" maxlength="30" required></input><br>
            <input type="text" placeholder="Last Name" name="LastName" maxlength="30" required></input><br>
            <input type="text" placeholder="Email address"name="Emailaddress" maxlength="320" required></input><br>
            <input type="password" placeholder="Password" name="Password" required></input><br>
            <input type="password" placeholder="Confirm Password" name="ConfirmPassword" required></input><br>

            <!--Captcha-->
            <p></p>
            <div class="elem-group">
           
                
                <img src="captcha/captcha.php" alt="CAPTCHA" class="captcha-image"><i class="fas fa-redo refresh-captcha"></i>
                <!-- idk what class does --> <!--Class usually is used for the css-->
                <br><p></p>
                <input type="text" placeholder="Enter CAPTCHA" id="captcha" name="captcha_challenge" pattern="[A-Z]{6}">
            </div>
            <div>
            <label class="form-check-label" for="form2Example3">
                      I agree all statements in <a href="#!">Terms of service</a>
                    </label>
            </div>
            <input type="submit" name="registerAccount"><br><br>
        </form> 

        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque gravida tempor ris
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque gravida tempor ris 
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque gravida tempor ris
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque gravida tempor ris
        </p>

        </div> 
        </div>     </div> 
    </div> 
    </div> <!--cointer text-->

    </div></div></div>
    <!--/page-->
    </div> <br>

    
    <div class="row" style="background-color:#FFFFFF">
    <div class="text-center width="1050" height="700"" style="background-color:#FFFFFF">
    <div class="col-sm-12" style="background-color:#FFFFFF">
      <h3>Map</h3>
      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elitLorem ipsum dolor sit amet, consectetur adipisicing elitLorem ipsum dolor sit amet, consectetur adipisicing elit...</p>
      <img src="worlds.png" class="img-fluid" width="1050" height="700" style="background-color:#FFFFFF">
  </div>
  <h3 class="text-white">Map</h3>



  <div class="p-3 mb-2  text-white" style="background-color:#008190">

  <div class="container" >
  <div class="row">

    <div class="col-sm-4" >
      <h3>Our system</h3>
      <?php instructions();?>
    </div>
    <div class="col-sm-4">
      <h3>Public data</h3>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque gravida tempor risus ut faucibus. Donec a metus sollicitudin est posuere hendrerit. Fusce vel nisi maximus, consequat lectus ut, dictum sem. Quisque nec ex sed ex facilisis consectetur. Mauris id sem non dolor elementum tempor at consequat mauris. Aenean imperdiet ipsum nisl, in varius enim fringilla quis. Nullam sollicitudin turpis ex, eu venenatis sem volutpat ac. Cras quam enim, lacinia vel aliquam sit amet, commodo sed ligula. Nullam et aliquam elit, vel euismod ipsum. Suspendisse luctus nec orci eget tristique. Nam pulvinar molestie diam, ut interdum felis pulvinar ut. Duis finibus lacus id risus imperdiet, at suscipit turpis aliquet.

  Aliquam viverra lectus odio, vel tempor nulla maximus ut. Cras quis malesuada velit. Aliquam ornare ornare lacus a tincidunt. Etiam ut nibh ultrices, tincidunt turpis ac, luctus diam. Integer finibus, ex sed aliquam efficitur, orci risus ultrices est, at varius augue enim nec dui. Donec facilisis pretium felis eu luctus. Pellentesque dui quam, laoreet vitae cursus vel, laoreet a neque. Phasellus in diam vitae dolor viverra commodo. Integer convallis sodales cursus. Integer et augue nec turpis vestibulum pellentesque. Nunc ac dui sed mi dignissim pellentesque. Nunc condimentum justo vitae lorem mollis, eget tristique risus faucibus. Maecenas sed ullamcorper risus, eu venenatis.</p>
    </div>
    <div class="col-sm-4" >
      <h3>Our system</h3>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque gravida tempor risus ut faucibus. Donec a metus sollicitudin est posuere hendrerit. Fusce vel nisi maximus, consequat lectus ut, dictum sem. Quisque nec ex sed ex facilisis consectetur. Mauris id sem non dolor elementum tempor at consequat mauris. Aenean imperdiet ipsum nisl, in varius enim fringilla quis. Nullam sollicitudin turpis ex, eu venenatis sem volutpat ac. Cras quam enim, lacinia vel aliquam sit amet, commodo sed ligula. Nullam et aliquam elit, vel euismod ipsum. Suspendisse luctus nec orci eget tristique. Nam pulvinar molestie diam, ut interdum felis pulvinar ut. Duis finibus lacus id risus imperdiet, at suscipit turpis aliquet.
  Aliquam viverra lectus odio, vel tempor nulla maximus ut. Cras quis malesuada velit. Aliquam ornare ornare lacus a tincidunt. Etiam ut nibh ultrices, tincidunt turpis ac, luctus diam. Integer finibus, ex sed aliquam efficitur, orci risus ultrices est, at varius augue enim nec dui. Donec facilisis pretium felis eu luctus. Pellentesque dui quam, laoreet vitae cursus vel, laoreet a neque. Phasellus in diam vitae dolor viverra commodo. Integer convallis sodales cursus. Integer et augue nec turpis vestibulum pellentesque. Nunc ac dui sed mi dignissim pellentesque. Nunc condimentum justo vitae lorem mollis, eget tristique risus faucibus. Maecenas sed ullamcorper risus, eu venenatis.</p>
    </div>

  </div>
  </div>
  </div>

   <!-- Contact Section-->
        <section class="page-section " id="contact" style="background-color:#FFFFFF">
            <div class="container">
                <!-- Contact Section Heading-->
                <h2 class="text-black">Contact us</h2>
                <!-- Icon Divider-->
                <div class="divider-custom">
                    <div class="divider-custom-line"></div>
                    <div class="divider-custom-icon"></div>
                    <div class="divider-custom-line"></div>
                </div>
                <!-- Contact Section Form-->
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-xl-7">
                        <!-- * * * * * * * * * * * * * * *-->
                        <!-- * * SB Forms Contact Form * *-->
                        <!-- * * * * * * * * * * * * * * *-->
                        <!-- This form is pre-integrated with SB Forms.-->
                        <!-- To make this form functional, sign up at-->
                        <!-- https://startbootstrap.com/solution/contact-forms-->
                        <!-- to get an API token!-->
                        <form id="contactForm" data-sb-form-api-token="API_TOKEN">
                            <!-- Name input-->
                            <div class="form-floating mb-3">
                                <input class="form-control" id="name" type="text" placeholder="Enter your name..." data-sb-validations="required" />
                                <label for="name">Full name</label>
                                <div class="invalid-feedback" data-sb-feedback="name:required">A name is required.</div>
                            </div>
                            <!-- Email address input-->
                            <div class="form-floating mb-3">
                                <input class="form-control" id="email" type="email" placeholder="name@example.com" data-sb-validations="required,email" />
                                <label for="email">Email address</label>
                                <div class="invalid-feedback" data-sb-feedback="email:required">An email is required.</div>
                                <div class="invalid-feedback" data-sb-feedback="email:email">Email is not valid.</div>
                            </div>
                            <!-- Phone number input-->
                            <div class="form-floating mb-3">
                                <input class="form-control" id="phone" type="tel" placeholder="(123) 456-7890" data-sb-validations="required" />
                                <label for="phone">Phone number</label>
                                <div class="invalid-feedback" data-sb-feedback="phone:required">A phone number is required.</div>
                            </div>
                            <!-- Message input-->
                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="message" type="text" placeholder="Enter your message here..." style="height: 10rem" data-sb-validations="required"></textarea>
                                <label for="message">Message</label>
                                <div class="invalid-feedback" data-sb-feedback="message:required">A message is required.</div>
                            </div>
                            <!-- Submit success message-->
                            <!---->
                            <!-- This is what your users will see when the form-->
                            <!-- has successfully submitted-->
                            <div class="d-none" id="submitSuccessMessage">
                                <div class="text-center mb-3">
                                    <div class="fw-bolder">Form submission successful!</div>
                                    To activate this form, sign up at
                                    <br />
                                    <a href="https://startbootstrap.com/solution/contact-forms">https://startbootstrap.com/solution/contact-forms</a>
                                </div>
                            </div>
                            <!-- Submit error message-->
                            <!---->
                            <!-- This is what your users will see when there is-->
                            <!-- an error submitting the form-->
                            <div class="d-none" id="submitErrorMessage"><div class="text-center text-danger mb-3">Error sending message!</div></div>
                            <!-- Submit Button-->
                            <button class="btn btn-primary btn-xl disabled" id="submitButton" type="submit">Send</button>
                            

                        </form>
                    </div>
                </div>
            </div>
        </section> </div>
  </div>
  </div>
  <br>
  </div>

</main>

</div>
</form>
    <?php pagefooter();?>
</body>
</html>