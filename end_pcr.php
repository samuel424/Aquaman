<form action="form_pcr.php" method="POST" style="border:1px solid #ccc">
        <div>
            <h2>Enter run data</h2>
            <?php
                $lab = intval($_SESSION['UserLabID']);
                if (!$resultmachine = mysqli_query($link, "SELECT MachineID, MachineModel FROM qPCRMachine AS q 
                LEFT JOIN LabAffiliation AS l 
                ON q.MachineLab = l.LabID WHERE l.LabID = $lab GROUP BY q.MachineID")){
                    echo "Failed to query database for Machine options";
                }
            ?>
            </select><br>
                <label for='qPCRMachine'><b>qPCRMachine:</b></label>
                <select name='machine' id='machine' require>
                    <?php
                        // Echo leader options for the drop down list
                        while($row = mysqli_fetch_row($resultmachine)) {
                            $catid = $row[0];
                            $catname = $row[1];
                            echo "<option value='$catid'>$catname</option>"; 
                        }
                    ?>
                </select><br>
                <label for="kit"><b>Kit:</b></label>
                <input type="text" name="kit" size = "20" maxlength="50" required><br>
                <label for='Cycling'><b>Cycling conditions:</b></label>
                <select name='cycling' id='cycling' require>
                    <?php
                        // Echo leader options for the drop down list
                        while($row = mysqli_fetch_assoc($resultcycling)) {
                            $catid = $row["CyclingID"];
                            $catname = $row["CyclingCond"];
                            echo "<option value='$catid'>$catname</option>"; 
                        }
                    ?>
                </select><br>
                <?php
                    inputDate(TRUE);
                ?>
                <input type="submit" value="Register run"><br>
        </div>
            </form><br>

            