<!--house_chem.php-->
<?php
$hk_sql = "SELECT GeneID, GName 
FROM gene WHERE TargetSpecies = $species 
AND Housekeeping = 1 
AND GeneID IN 
(SELECT TargetGene FROM qpcrdata AS q 
LEFT JOIN probe AS p ON q.Probe = p.ProbeID 
GROUP BY TargetGene)";
$result_hk = mysqli_query($link, $hk_sql);
?>
<p><b>Select housekeeping gene (only showing genes with PCR data)</b></p>
<form action='form_chemanalysis.php' method='post'>
<label for='hkgene'>Housekeeping gene:</label>
    <select name='HouseID' id='HouseID'>
        <?php
            // Echo species options for the drop down list
            while($row = mysqli_fetch_assoc($result_hk)) {
                $catid = $row["GeneID"];
                $catname = $row["GName"];
                echo "<option value='$catid'>$catname</option>"; 
            }
            ?>
<input type='submit' value = 'Continue' name='House'>
        </form>

        