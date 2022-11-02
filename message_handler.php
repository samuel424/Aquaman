<?php
include 'fetch_last.php';

function error_check(){
    /**
     * Fucntion output messages
    */
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            // Success message
            if (isset($_GET['success'])) {
                
                // Insert
                if ($_GET['success'] == 'added'){
                    echo $_GET['item']." successfully added";
                }

                // login
                if ($_GET['success'] == 'login') {
                    echo 'Successfully logged in';
                }
                // logout
                if ($_GET['success'] == 'logout') {
                    echo 'Successfully logged out';
                }
                // laboratory
                if ($_GET['success'] == 'laboratory'){
                    echo "Laboratory successfully registered";
                }

                // location
                if ($_GET['success'] == 'location'){
                    echo "Location added successfully";
                    $number = $_GET['number'];
                    $new_entries = array_reverse(confirm_entry('SamplingLocation', 'LocationID', 1, 1));
                    echo '<br><b>ID of new location: '.$new_entries[0];
                    echo '</b>';
                }
                // Rawmaterial
                if ($_GET['success'] == 'fieldsampling'){
                    echo "Field Sampling successfully registered";
                }
                // Fish
                if ($_GET['success'] == 'fish'){
                    echo "Fish successfully registered";
                    $nm = $_GET['nm'];
                    $nf = $_GET['nf'];
                    if ($nm > 0){
                        //males added first
                        $new_males = array_reverse(confirm_entry('FishIndividual', 'FishID', $nf+1, $nm));
                        echo '<br><b>ID of new fish (males): ';
                        foreach($new_males as $new_male){
                            echo $new_male . ", ";
                        } 
                    }
                    if ($nf > 0) {
                        $new_females = array_reverse(confirm_entry('FishIndividual', 'FishID', 1, $nf));
                        echo '<br><b>ID of new fish (females): ';
                        foreach($new_females as $new_female){
                            echo $new_female . ", ";
                        } 
                    }
                    echo '</b>';
                }
                // Sample
                if ($_GET['success'] == 'sample'){
                    echo "Sample added successfully";
                    $number = $_GET['number'];
                    $new_entries = array_reverse(confirm_entry('Ssample', 'SsampleID', 1, $number)); 
                    echo '<br><b>ID of new samples: ';
                    
                    foreach($new_entries as $new_entries){
                        echo $new_entries . ", ";
                    }
                    
                    echo '</b>';
                }
                // RNA extraction
                if ($_GET['success'] == 'RNAExtraction'){
                    echo "Sample added successfully";
                    $number = $_GET['number'];
                    $new_entries = array_reverse(confirm_entry('rnasample', 'RNAID', 1, $number)); 
                    echo '<br><b>ID of new RNAs: ';
                    
                    foreach($new_entries as $new_entries){
                        echo $new_entries . ", ";
                    }
                    
                    echo '</b>';
                }

                // qPCRRun
                if ($_GET['success'] == 'qpcrrun'){
                    echo "Run added successfully";
                    $new_entries = array_reverse(confirm_entry('qPCRrun', 'qPCRrunID', 1, 1));
                    echo '<br><b>ID of new run: '.$new_entries[0];
                    echo '</b><br>';
                    echo '<a href="_welldata.txt">Download plate file (txt)</a><br>';
                    echo '<a href="qpcr.php">Report results</a>';
                }
                if ($_GET['success'] == 'freezer_out') {
                    echo 'Successfully took out sample from freezer';
                }

                // qPCRData
                if ($_GET['success'] == 'updateqpcrrun') {
                    echo 'PCR results reported successfully';
                }
                
            } elseif (isset($_GET['error'])) {
                //login
                if ($_GET['error'] == 'login') {
                    echo "Error: You are not logged in";
                }
                //database
                if ($_GET['error'] == 'sql') {
                    echo 'Error: Problem connecting server';
                }
                //laboratory
                if ($_GET['error'] == 'laboratory'){
                    echo "Please create or join a laboratory";
                }    
                // Not complex enought
                if ($_GET['error'] == 'complex') {
                    echo "Error: " .$_GET['item'] ." is not complex enought. 
                    It must be longer than 8 character and contain at least 
                    one number, one lowercase and a uppercase";
                }

                // Invalid or unset
                if ($_GET['error'] == 'invalid') {
                    echo "Error: " .$_GET['item'] ." invalid or unset";
                }
                // Select at least 1
                if ($_GET['error'] == 'select') {
                    echo "Error: Select at least one " .$_GET['item'];
                }
                // To long text
                if ($_GET['error'] == 'long') {
                    echo "Error: ".$_GET['item']." can't be longer than ".$_GET['max']." characters";
                }
                // Outside allowed
                if ($_GET['error'] == 'outside') {
                    echo "Error: ".$_GET['item'];
                    if (!empty($_GET['max'])) {
                        echo ", must be less than ".$_GET['max'];
                    }
                    if (!empty($_GET['min'])) {
                        echo ", must be more than ".$_GET['min'];
                    }
                }

                //---- DATABASE ERRORS ----//
                // Exists
                if ($_GET['error'] == 'exists') {
                    echo "Error: ".$_GET['item']." already exist";
                }
                //Doesnt exist
                if ($_GET['error'] == 'notinDB') {
                    echo "Error: ".$_GET['item']." is not in database";
                }
                // Search results
                if ($_GET['error'] == 'noresults') {
                    echo "Search failed: No results";
                }
                
                
                //---- USER ACCOUNT ERRORS ----//
                //captcha
                if ($_GET['error'] == 'captcha') {
                    echo 'Error: Fill in captcha to prove your not a robot';
                }
                
                if ($_GET['error'] == 'pwd_simple') {
                    echo "Error: Password not strong enough";
                }
                if ($_GET['error'] == 'confirm_pwd') {
                    echo "Error: Confirm password is not the same";
                }
                //name
                if ($_GET['error'] == 'invalid_name') {
                    echo "Error: First name invalid or unset";
                }
                if ($_GET['error'] == 'invalid_creds') {
                    echo "Error: Incorrect email or password";
                }
                
                //Location 
                if ($_GET['error'] == 'coordinate') {
                    echo "Error: Please enter both coordinates";
                }
                if ($_GET['error'] == 'lettersentered') {
                    echo "Error: No letters alowed in number field";
                }
                // Date
                if ($_GET['error'] == 'date') {
                    echo "Error: Date is incorrect, date should be ' YYYY-MM-DD hh:mm '";
                }
                
                // Fish
                if ($_GET['error'] == 'nofish') {
                    echo "Error: No such fish in database";
                }
                // PCR
                if ($_GET['error'] == 'house') {
                    echo "Please choose at least one housekeeping gene";
                }
                if ($_GET['error']=='multiplespecies'){
                    echo 'Please only select genes from one species';
                }
                if ($_GET['error'] == 'primer') {
                    echo "Please select exactly one forward primer, one reverse primer, and one probe per gene";
                }        
            }
        }
    }
?>