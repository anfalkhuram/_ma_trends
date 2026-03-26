<?php
    function getArray($str){
        echo '<pre>';
        print_r ($str);
        echo '</pre>';
    }

    function getSaveValue($conn, $str){
        $str = trim($str);
        $str = mysqli_real_escape_string($conn, $str);
        $str = htmlentities($str);
        return $str;
        
    }
?>