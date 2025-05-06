<?php
if (!function_exists('getConn')) {
    function getConn () {
        static $conn;
        if ($conn===NULL){ 
            $path = $_SERVER['DOCUMENT_ROOT'];
            $configs = include($path . '/functions/.config.php');
            extract($configs);
            $conn = mysqli_connect($servername, $username, $password, $dbname);
        }
        return $conn;
    }
}