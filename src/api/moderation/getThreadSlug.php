<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;

// Get connection
$conn = getConn();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if(!session_id()) {
  session_start();
} 

if(include($path . "/functions/validateSession.php")) {
    if (isset($_GET["i"])) {

        $id = $_GET["i"];

        $conn = getConn();
        
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT u.clearance FROM users u 
                WHERE u.user_id = '$user_id'
                LIMIT 1";

        $result = $conn->query($sql);

        if($result->num_rows !== 1) {
            echo "An error has occured MR0";
            die();
        }

        $clearance = (int)$result->fetch_assoc()["clearance"];

        if($clearance < 1) {
            die();
            echo "Authorization not high enough";
        }

        $sql = "SELECT slug FROM threads WHERE id = '$id'";
        
        $result = $conn->query($sql);

        if($result->num_rows !== 1) {
            echo "An error has occured MR1";
            die();
        }

        $cont = $result->fetch_assoc()["slug"];

        echo $cont; 
        
    } else {
        echo "An error has occured MR3";
    }

} else {
    echo "Please login";
}