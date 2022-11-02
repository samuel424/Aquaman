<?php
session_start();

// redirects from unwanted pages if not logged in
$thispage = basename($_SERVER['SCRIPT_NAME']);
if (empty($_SESSION['aqualogin'])) {
    if ($thispage != 'index.php' && $thispage != 'login.php' && 
    $thispage != 'register.php' && $thispage != 'registerConfirm.php' &&
    $thispage != 'form_contact.php') {
        header('Location: index.php?error=login');
    }
}elseif ($_SESSION['aqualogin'] != 'wearein') {
    if ($thispage != 'index.php' && $thispage != 'login.php' && 
    $thispage != 'register.php' && $thispage != 'registerConfirm.php' &&
    $thispage != 'form_contact.php') {
        header('Location: index.php?error=login');
    }
} else {
    if ($thispage == 'login.php' or $thispage == 'register.php' or $thispage == 'registerConfirm.php') {
        header('Location: index.php?success=login');
    }
}

function clearfolders($userdir, $create = TRUE, $keepdata = FALSE, $removeresultdir = FALSE, $removedir = FALSE){
    $resultdir = $userdir.'/results';
    if ($create){
        if(!file_exists($userdir)){ // no user dir
            mkdir($userdir);
            mkdir($resultdir);
        } elseif (!file_exists($resultdir)){ // user dir exists but no result dir
            mkdir($resultdir);
        }
    }

    $resultfiles = glob($resultdir.'/*');  // deletes user files (result files deleted later)
    foreach ($resultfiles as $file){
        unlink($file);
    }
    if (!$keepdata){
    $userfiles = glob($userdir.'/*');

    if (($key = array_search($userdir.'/results', $userfiles)) !== false) {
        unset($userfiles[$key]);
    }

    foreach ($userfiles as $file){
        unlink($file);
    }
    }
    
    if($removeresultdir){
        rmdir($resultdir);
    }
    if($removedir){
        rmdir($userdir);
    }
}

// clear temporary vars and files
if ($thispage != 'form_chemanalysis.php' && $thispage != 'add_map.php') {
    $variables = ['species', 'house', 'sampling_ids', 'chem', 'housename',
                'speciesname', 'do_analysis', 'noise', 'do_noise', 'redo_noise'];
    foreach ($variables as $var){
        if (isset($_SESSION[$var])){
            unset($_SESSION[$var]);
        }
    }

    $user = $_SESSION['UserID'];
    if (file_exists('regdata'.$user)){
        $userdir = 'regdata'.$user;
        clearfolders($userdir, $create = FALSE, $keepdata = FALSE, $removedir = TRUE, $removeresultdir = TRUE);
    }
    if (file_exists('mapdata'.$user)){
        $userdir = 'mapdata'.$user;
        clearfolders($userdir, $create = FALSE, $keepdata = FALSE, $removedir = TRUE, $removeresultdir = TRUE);
    }
}

function pageheader(){
    
    print "
    <script>
    /* Clicking drop button*/

    function holdDrop(x) {
    document.getElementById(x).classList.toggle('show');
    }

    // Close dropdown if click somewere else
    window.onclick = function(event) {
        if (!event.target.matches('.drop')) {
            var dropdowns = document.getElementsByClassName('droplist');
            var i;
            for (i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
                }
            }
        }
    }

    </script>
    ";

    print "<div class = 'PageHeader'>"; // Satrt page header

    print "<div >
    <img src='images\Åquamän.png' id='mask' alt=''>
    <header  style='color:black;margin-left 1rem'>&#8205;  Åquamän</header></div>"; 


    print "<nav><ul><li>";

    // Home button
    print "<form action='index.php'><button type='Home' class='drop'>Home</button></form>";

    if (empty($_SESSION['aqualogin'])) {
        // Login
        print "<li><form action='login.php'><button type='Login' class='drop'>Login</button></form></li>";
        // Register
        print "<li><form action='register.php'><button type='Register' class='drop'>Register</button></form></li>";
        
    } elseif ($_SESSION['aqualogin'] == 'wearein') {
        
        if($_SESSION['LabAdmin'] == 1){
            print "<li><div class='dropdown'>
                <button onclick='".'holdDrop("Target")'
                ."' class='drop'>Target species</button>
                <div id='Target' class='droplist'>
                <a href='form_species.php'>New Species</a>
                <a href='form_gene.php'>New Gene</a>
                <a href='form_probe.php'>New Probe</a>
                </div>
            </div></li>";
        }

        print "<li><div class='dropdown'>
            <button onclick='".'holdDrop("Location")'
            ."' class='drop'>Sampling location</button>
            <div id='Location' class='droplist'>
            <a href='form_location.php'>Location</a>
            <a href='form_noise.php'>Report Noise</a>  
            </div>
        </div></li>";

        print "<li><div class='dropdown'>
            <button onclick='".'holdDrop("Expedition")'
            ."' class='drop'>Expedition</button>
            <div id='Expedition' class='droplist'>";
        if ($_SESSION['LabAdmin'] == 1) {
            print "<a href='form_catchmethod.php'>New Capture Method</a>";
        }
        print "<a href='form_rawmaterial.php'>Report Sampling</a>
            <a href='form_fish.php'>Report Fish</a>  
            </div>
        </div></li>";

        print "<li><div class='dropdown' >
            <button onclick='".'holdDrop("Labwork")'
            ."' class='drop'>Labwork</button>
            <div id='Labwork' class='droplist'>
            <a href='form_sample.php'>Report Dissection</a>
            <a href='form_RNA.php'>Report Extraction</a>
            <a href ='form_pcr.php'>Initiate qPCR run</a>
            <a href='qpcr.php'>Report qPCR run</a>
            <a href='form_freeze.php'>Freeze Sample</a>
            </div>
        </div></li>";

        print "<li><div class='dropdown'>
            <button onclick='".'holdDrop("Laboratory")'
            ."' class='drop'>Laboratory</button>
            <div id='Laboratory' class='droplist'>
            <a href='form_affiliation.php'>Manage Laboratory</a>";
        if ($_SESSION['LabAdmin'] == 1) {
            print "<a href='form_freezer.php'>Add Freezers</a>
                <a href='form_machine.php'>Register qPCR machine</a>";
        }
        print "</div>
        </div></li>";

        print "<li><div class='dropdown'>
            <button onclick='".'holdDrop("Results")'
            ."' class='drop'>Results</button>
            <div id='Results' class='droplist'>
            <a href='form_map.php'>Map</a>
            <a href='form_chemanalysis.php'>Correlations chemical data</a>"
            //."<a href=''>Compare populations</a>"
            ."</div>
        </div></li>";

        print "<li><div class='dropdown'>
            <button onclick='"
            .'holdDrop("Account")'
            ."' class='drop'>Account</button>
            <div id='Account' class='droplist'>
            <a href='accountSettings.php'>Account</a>
            <a href='loggingout.php'>Log Out</a>
            </div>
        </div></li>";

        print "<lab style='color:white'>Current lab:<br>";
        print $_SESSION['UserLab'];
        if ($_SESSION['LabAdmin'] == 1) {print '(Admin)';}
        print '</lab>';

    } else {
        // Login
        print "<li><form action='login.php'><button type='Login' class='drop'>Login</button></form></li>";
        // Register
        print "<li><form action='register.php'><button type='Register' class='drop'>Register</button></form></li>";
    } 

    print "</ul></nav>"; //End navigation

    print "</div> <!--PageHeader-->"; //End Page header

    //Errorcheck
    include 'message_handler.php';
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {error_check();}
}

function pagefooter(){
    print "<footer>";
    print '<!-- Footer-->
    <footer class="footer text-center text-white" style="font-family: Trirong">
    <br>
        <div class="container">
            <div class="row">
                <!-- Footer Location-->
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <h4 class="text-uppercase mb-4">Location</h4>
                    <p class="lead mb-0">
                        Uppsalagatan
                        <br />
                        UU, MO 65243
                    </p>
                </div>
                <!-- Footer Social Icons-->
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <h4 class="text-uppercase mb-4">Around the Web</h4>
                    <a class="btn btn-outline-light btn-social mx-1" href="#!"><i class="fab fa-fw fa-facebook-f"></i></a>
                    <a class="btn btn-outline-light btn-social mx-1" href="#!"><i class="fab fa-fw fa-twitter"></i></a>
                    <a class="btn btn-outline-light btn-social mx-1" href="#!"><i class="fab fa-fw fa-linkedin-in"></i></a>
                    <a class="btn btn-outline-light btn-social mx-1" href="#!"><i class="fab fa-fw fa-dribbble"></i></a>
                </div>
                <!-- Footer About Text-->
                <div class="col-lg-4">
                    <h4 class="text-uppercase mb-4">About Åquamän</h4>
                    <p class="lead mb-0">
                    Åquamän is a free to use<br> LIMS-system by 
                        <a href="http://localhost/Aquaman/#section4" style="color:#fff"> Some students</a>
                        .
                    </p>
                </div>
            </div>
        </div>
    </footer>';
    print "</footer>";
}

function r_non_num($float) {
    return preg_replace("/[^0-9.]/", "", $float);
}

function inputDate($time= True) {
    echo 
    "<label for='date'>Date:</label> 
    <input type='date' name='date' value=".date('Y-m-d')." required>";
    if ($time) {
        echo
        "<label for='time'>Time:</label>
        <input type='number' name='hour' class='time' size='2' max='23' min='0' placeholder='hh' value=".date('H')." required>
        <input type='number' name='minute' class='time' size='2' max='60' min='0' placeholder='mm' value=".date('i')." required>";
    }
    echo '<br>';
}

function inputLocation($link) {
    $sql = "SELECT * FROM `SamplingLocation`";
    $reslocation = mysqli_query($link, $sql);
    if(!$reslocation){
        echo "Failed to load Location";
    }
    
    echo "<label for='location'>Location:</label>";
    echo"<select name='location' required>";
    while($row = mysqli_fetch_assoc($reslocation)) {
        $catid = $row["LocationID"];
        $catname = $row["LocationName"];
        $Lat = $row["CorLatitude"];
        $Long = $row["CorLongitude"];
        echo "<option value='$catid'>$catname longtidue:$Long Latitude: $Lat</option>"; 
    }
    echo "</select><br>";
}

function instructions() {
    echo "
        <p>
            To use the Åquamän tool you first need to add the species to the database. 
            This is done by a laboratory admin. 
            <ol>
            <li>Set up target species in Target/Target species form.</li>
            <li>The stress genes and at least one houskeeping gene should be set up using the Target/New Gene form.</lis>
            <li>Finally the forward and reverse primer as well as the probe for the gene must be registered through the Target/New Probe form.</li>
            </ol>

            Now you are ready to do an expedition!<br>
            <ol>
            <li>Register Sampling location/Location form.</li>
            <li>Expeditions is, once you have the result of chemical data, in Expedition/Report Sampling. </li>
            <li>The fish caught in Expedition/Report Fish.</li>
            <li>When the fish are dissected, the tisseus are reported in Labwork/Report Dissection.</li>
            <li>Thereafter, the RNA extraction is reported in Labwork/Report Extraction.</li>
            <li>Set up your qPCR through Labwork/Initiate qPCR run.</li>
            <li>Result is then registered in the Labwork/Report qPCR run.</li>
            </ol>
            
            Remember to register when you take samples in and out of the freezer in the Labwork/Freeze Sample.
        </p>
    ";
}

function Oursystem() {
    echo "<p>"
            // Some background
            ."Fish are important. 
            Fish have thought history been one of the most important sources for human beings.
            But today the survival of fish are threatened.
            The fish are also important parts of the aquatic ekosystems. 
            If the fish where to go extinct, 
            something that today for the first time in history is of a real risk of happening, 
            this whould surely destabelize the rest of the ekosystems they belong to.  
            Overfishing and polution have taken a tole on the fish populations, 
            and the ocean that has earlier been seen as an infinite source of fish become empty.
            <br><br>"
            // Investigating stress
            ."Therefore there is today a clear need for investigating the stess factors for fish. 
            That is where the Åquamän system comes into play. 
            The Laboratory Information Management System is constructed to facilitates studies of 
            the stress level of fish through the expression of stress genes. 
            The system has been designed around a pipline for investigating the expression on the RNA level
            through Reverse Transcription - Quantitative Polymerase Chain Reaction.
            <br><br>"
            //Traceability
            ."The main benefit of the Åquamän system is the high focus on tracebility. 
            For each step you, the researcher, should record a timestamp. 
            Åquamän also has an inbuilt system for reporting when a sample is inserted into and 
            taken out of freezers, so that its easy to track how old a sample is and 
            how much of that time the sample has spent outside the freezer.
        </p>
    ";
}

?>