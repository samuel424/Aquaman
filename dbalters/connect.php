<?php
    function connect($root = FALSE){
        /**
         * Function that connects to the database
         * If $root = TRUE only connects to the localhost
         */

        $hostname = "localhost";
        $username = "root";
        $password = "";
        $db = "aquaman";

        if ($root) {
            $link = mysqli_connect($hostname, $username, $password);
        } else {
            $link = mysqli_connect($hostname, $username, $password, $db);    
        }
        if (!$link) {echo "Error: Unable to connect." . mysqli_connect_error() . PHP_EOL; exit;};
        return $link;
    }
    function dconnect($link) {
        /**
         * disconnects
         */
        mysqli_close($link) or die("Unable to close connection");
    }
?>