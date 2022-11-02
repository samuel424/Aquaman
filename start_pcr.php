<div class="body">
        <h1>New qPCR RUN</h1>
        <p> Please fill in this form below to initiate a new qPCR run:</p>
        <?php
        //If post empty, start by selecting genes
        if (empty($_POST)){
        echo "<form action='form_pcr.php' method='POST'>";
            echo '<label for="Gene"><b>Genes to test:</b> <p>If your gene does not show up: <ul><li>make sure forward primer, reverse primer and probe are set</li></ul></p></label><br>';
            //Fetch rows as [TargetGene, GeneName, SpeciesName, Housekeeping], ordered by TargetGene
                while($row = mysqli_fetch_row($resultgene)){
                    if ($row[3]==0){    //stress
                        echo '<input type = "checkbox" name="g_'.$row[1].'" value="'.$row[0].'">';
                        echo $row[1] . '<i> ' . $row[2].'</i>';
                    } else{             //housekeeping
                        echo '<input type = "checkbox" name="g_h_'.$row[1].'" value="'.$row[0].'">';
                        echo $row[1] . '<i> ' . $row[2].'</i> (Housekeeping)';
                    }
                        echo '<br>';
                }
            echo '<br><input type="submit" value="Choose genes"><br>';
            echo '</form>';
            echo '<br><a href=search_pcr.php>Search for registered runs</a>';
            }
            ?>
            