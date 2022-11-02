<?php
    //If not loged in header('Location: index.php')
    include 'connect.php';
    include "standardAssets.php";
    $link = connect();
    if(!$result_user = mysqli_query($link,"SELECT * FROM `Account`")){
        echo "Failed to query database for UserID options";
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
            <link href='https://fonts.googleapis.com/css?family=Vampiro One' rel='stylesheet'>
            <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Rancho&effect=fire-animation">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
            
  <script src="scroll-down.js"></script>
  <style>
    h1 {
        font-family: Trirong;
      font-size: 4.375rem;
      text-align: center;
      filter: drop-shadow(0 0 0.75rem #ffffff);
    }

    h2 {
        font-family: Trirong;
        color: ##ffffff;
        filter: drop-shadow(0 0 0.75rem #ffffff);

    }
    h3 {
        font-family: Trirong;
          color: ##ffffff;
          filter: drop-shadow(0 0 0.75rem #ffffff);

    }

    hsmall {
      font-family: "Trirong", serif;
      font-size: 1.25rem;

    }

    body {
    background-color: #222222;
        background: repeating-linear-gradient(
            45deg,
            #2b2b2b 0%,
            #c06442 10%,
            #222222 0%,
            #222222 50%
          )
          0 / 15px 15px;
      }


    }

  </style>

</head>
<body>
    <?php pageheader();?>
    <!--Content-->
    
    <section class="company-heading intro-type" id="parallax-one"> <!--Welcome text-->
      <div class="container">
          <div class="row ">
            <div class="col-md-12">
              <div class="jumbotron text-center">
                <c  style="color:black;">Welcome to Åquamän</c> <br><br><br>
                <img src="Åquamän.png" class="img-fluid" id="mask"  style="width: 28rem;">
                <br> 
                <a   href="#section2"><img alt="Arrow Down Icon"  id="mask" src="down_o1.png" style="height: 3rem;" ></a>
              </div>
            </div>
          </div>
        </div>
    </section> <!--Welcome text-->

  <div class="body">
    <div class="main">
      <br><br>

    <div class="jumbotron  text-white" style="background-color:#3b8262; height: 60rem;">
        <section id="section2"><!--Green-->
        <div class="container " >
            <div class="row">
                <div class="col-sm-4" >
                    <div class="rowpad">
                        <h3>Our system</h3>
                        <p><?php Oursystem()?></p>
                    </div>
                </div>  
          
                <div class="col-sm-4">
                    <div class="rowpad">
                        <h3>Background</h3>
                        <p>The method of choice for quantifying stress gene expression is Reverse Transcription - Quantitative Polymerase Chain Reaction (RT-qPCR), the gold standard for quantification of mRNA. What genes, species, and tissues are used for a good comparison is left for the customer to decide based on their biological expertise. Specific primers and probes for the RT-qPCR should be supplied by the customer for each species. The four genes below are used for testing during the LIMS development of the system, however, the customer can enter their own genes of interest for analysis.<p>
<p><i>hsp70</i>, heat shock protein 70, is the most studied of the heat shock proteins. It increases in response to heat stress, pathogens, and chemical pollution.</p> 
<p><i>sod1</i>, superoxide dismutase, increases in response to oxidative stress which can arise from metabolic abnormalities, temperature and salinity changes, and heavy metal pollution.</p>
<p><i>igf1</i>, Insulin-like growth factor 1, is produced in the liver. Main hormone regulating growth. Stressed fish express less Igf1 - a stressed fish would thus grow less.</p>
<p><i>elfa</i>, Elongation factor 1-alpha, is a housekeeping gene required for protein synthesis. The most stable of the housekeeping genes across sex and chemical stressors in zebrafish.</p>


                    </div>
                </div>   
          
                <div class="col-sm-4 text-primary" >
                    <div class="rowpad">
                        <div class="container form-control" style= "border:2px solid #020201; background-color:#c06442;  box-shadow: 2px 4px 16px 2px #000000;" >
                        <div class="col-sm-12 text-white " >
                            <h3>Use Åquamän</h3>        
                            <div class="page">
                            <?php instructions();?>
                            
                            </div> 
                        </div> 
                        </div> 
                    </div>
                </div> <!--textprimary-->

            </div> <!--row-->
        </div> <!--container--> 
        </section><!--Green-->
        <br><br>
        <div class="samcontainer" >
            
        <a  href="#section3"><img alt="Arrow Down Icon" class="center" id="mask" src="down_o1.png"  style="height: 3rem;"></a> 
        </div>
    </div> 
    
            
      <!--map-->
      <div class="row" >
      <section id="section3">
        <div class="jumbotron text-center width="1050" height="700" style="height: 60rem;">
          <div class="col-sm-12" >
            <div class="rowpad" >
                <p style="color:white">To create a map, the user accesses the front end of the map after login by going into the results menu.<br>There the user can choose a gene, a housekeeping gene, and a species from drop-down lists, subsequently can submit the selection, and the request is processed. </p>
                <img src="worlds.png" class="img-fluid" width="1050" height="700"  id="mask">
                <br>
                <a  href="#section4"><img alt="Arrow Down Icon" class="center" id="mask" src="down_o1.png"  style="height: 3rem;"></a> 
            </div>
          </div>
        </div>   
        </section>
    
      </div>        
              
      <!--About us-->   
      <section id="section4">  
      <div class="jumbotron  text-white " style="background-color:#3b8262; height: 60rem;" >
      
        <div class="container" >
            <div class="row "  >
              <div class="col-sm-7"  >
                  <div class="rowpad " style="padding-right:4rem"  >
                      <br><br><br>
                        <h3>About The Åquamän Team</h3>
                        <p>The website is made from seven students at Uppsala University, durring a coruse called "Syllabus for Information Management Systems". Our names are: Alexander Bergman, Elias Ekstedt, Lars Huson, Melina Martin, Andreas Medhage, Mathilda Stigenberg and Samuel Zargani. Currently we are all studying the masters program in bioinformatics. Some of us have a background in biology and others are students in the engineering program in molecualr biology.  If you wish to know more about our project read our report,available to you on the right. </p>
                        <br>
                        <h3>About The Åquamän system</h3>
                        <p>To monitor the well-being of fish, the Åquamän group developed a laboratory information management system (LIMS) tool for mapping the extent of fish stress in order to investigate causative agents and populations at risk and implement the most effective preventative measures. To measure the stress, the expression of stress related genes is investigated in fish tissues. Different species should be compared in different locations and their gene expression should be correlated to stressors, e.g. pH, oxygen content, and heavy metal levels in the water. Researchers can, once registered, enter data from different stages of the study into the database, as well as access information entered by other users, using the user interface. The system should also be able to provide simple analysis tools for researchers to evaluate their data . Throughout the process, a heavy focus lies on traceability of the samples, instruments, reagents, and people involved in the processing of each sample. As an output, several statistical analysis tools are available to correlate stress gene expression levels to the environmental parameters and sampling locations. This way, the LIMS can contribute to gather more comparative datasets between different research projects, and connect and relate fish data from different regions. The output shall also be displayed in a map, such that regional trends can be evaluated.</p>
                        
                    </div>
              </div>
              <div class="col-sm-5 text-primary" >
                  <div class="rowpad  "  >
                  <a  href="https://drive.google.com/file/d/1etyJK21lds1kvp8dRNXJUDfRlBoVriQu/view?usp=sharing"><img src="report.png" class="img-fluid" id="mask" width="510 " height="510" style="width: 29rem;padding-top: 5rem; padding-left: 2rem" > </a>
                    </div>
              </div>
            </div>
        </div>
        <br><br><br>
        <div class="samcontainer" > 
        <a  href="#section5"><img alt="Arrow Down Icon" class="center" id="mask" src="down_o1.png"  style="height: 3rem;"></a> 
        </div>
     
      </div>
      </section>       
    <div class="container " > 
    <section id="section5">
      <section class="jumbotron" style=" height: 40rem;"> <!-- Contact Section-->
      <div class="container text-center samcontainer" style=" padding-top: 6rem" >
        <div class="samcontainer contact"  >
          <!--Section heading-->
          <span style="color:white">
          <h2 class="h1-responsive font-weight-bold text-center my-4" style="font-size: 4.375rem;" >Contact us</h2>
            <br>
          <!--Section description-->
          <p class="text-center w-responsive mx-auto mb-5">Do you have any questions? Please do not hesitate to contact us directly</p>
          
          <div class="row" >
     
            <!--Grid column-->
            <div class="col-md-9 mb-md-0 mb-5">
              <form id="contact-form" name="contact-form" action="mail.php" method="POST">
        
                    <!--Grid row-->
                    <div class="row">
        
                        <!--Grid column-->
                        <div class="col-md-6">
                            <div class="md-form mb-0">
                                <input type="text" id="name" name="name" class="form-control">
                                <label for="name" class="">Your name</label>
                            </div>
                        </div>
                        <!--Grid column-->
        
                        <!--Grid column-->
                        <div class="col-md-6">
                            <div class="md-form mb-0">
                                <input type="text" id="email" name="email" class="form-control">
                                <label for="email" class="">Your email</label>
                            </div>
                        </div>
                        <!--Grid column-->
        
                    </div>
                    <!--Grid row-->
        
                    <!--Grid row-->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="md-form mb-0">
                                <input type="text" id="subject" name="subject" class="form-control">
                                <label for="subject" class="">Subject</label>
                            </div>
                        </div>
                    </div>
                    <!--Grid row-->
        
                    <!--Grid row-->
                    <div class="row">
        
                        <!--Grid column-->
                        <div class="col-md-12">
        
                            <div class="md-form">
                                <textarea type="text" id="message" name="message" rows="2" class="form-control md-textarea"></textarea>
                                <label for="message">Your message</label>
                            </div>
        
                        </div>
                    </div>
                    <!--Grid row-->
        
              </form>
              <br>
              
              <div class="text-center text-md-left">
                  <a class="btn text-white" style= "border:2px solid #020201; background-color:#c06442;" onclick="document.getElementById('contact-form').submit();">Send</a>
              </div>
              <div class="status"></div>
              
            </div>
            
            <!--Grid column-->
        
            <!--Grid column-->
            <div class="col-md-3 text-center" style="padding-left:3rem">
                <ul class="list-unstyled mb-0">
                    <li><i class="fas fa-map-marker-alt fa-2x"></i>
                        <p>Uppsala, UU 75273, Sweden</p>
                    </li>
        
                    <li><i class="fas fa-phone mt-4 fa-2x"></i>
                        <p>+ 01 234 567 89</p>
                    </li>
        
                    <li><i class="fas fa-envelope mt-4 fa-2x"></i>
                        <p>aquaman.uu@gmail.com</p>
                    </li>
                </ul>
            </div>
            <!--Grid column-->
          </section>
          </div>

  

      </div>
    </div>
    </span>

</div>
  </div>
</section>
  </div>
  <br>
  </div>

 

</div>
</div>
    </div>
    </div> <!--/page-->
    <?php pagefooter();?>
</body>
</html>