<?php

function confirm_entry($table_name, $id_name, $start = 1, $end = 1){
    $link=connect();
    $query =  "SELECT * FROM $table_name ORDER BY $id_name DESC LIMIT $start, $end;";
    $sql = mysqli_query($link, $query);
    $added_ids = [];
    $i = 0;
    while($row = mysqli_fetch_row($sql)){
        $added_ids[$i]= $row[0]+1;
        $i = $i + 1;
    }
    dconnect($link);
    return $added_ids;
}

//"file.php?error=some_error&var=$var"


?>
