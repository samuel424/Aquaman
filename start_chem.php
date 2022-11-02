<p><b>Select species to analyse (only showing species with PCR data)</b></p>
<form action='form_chemanalysis.php' method='post'>
<label for='speciesID'>Species:</label>
    <select name='speciesID' id='speciesID'>
        <?php
            // Echo species options for the drop down list
            while($row = mysqli_fetch_assoc($result_species)) {
                $catid = $row["SpeciesID"];
                $catname = $row["SpeciesLatin"];
                echo "<option value='$catid'>$catname</option>"; 
            }
            ?>
<input type='submit' value = 'Continue' name='Species'>
        </form>