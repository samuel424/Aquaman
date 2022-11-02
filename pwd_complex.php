<?php
    function pwd_complex($pwd) {
        $res = FALSE;
        // Check length > 8
        if (strlen($pwd) > 8) { 
            // Check contain number
            if (preg_match('~[0-9]+~', $pwd)) { 
                //Check both lowercase and uppercase
                if (strtolower($pwd) != $pwd && strtoupper($pwd) != $pwd) { 
                    $res = TRUE;
                }
            }
        }
        return $res;
    }
?>