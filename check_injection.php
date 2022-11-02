<?php

function check_injection($link, $post){
    $data = [];
    foreach ($post as $key => $value){
        $input = "{$value}";
        $clean = mysqli_real_escape_string($link, $input);
        $data[$key] = $clean;
      }
    return $data;
}


?>