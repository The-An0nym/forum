<?php
if (!function_exists('getConn')) {
    function getConn () {
        static $conn;
        if ($conn === NULL){ 
            $path = $_SERVER['DOCUMENT_ROOT'];
            $configs = require_once $path . '/functions/.config.php';
            extract($configs);
            try{
                $conn = mysqli_connect($servername, $username, $password, $dbname);
            } catch (Exception $e) {
                require_once $path . '/500.html';
                die();
            }
        }

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }    
        return $conn;
    }
}