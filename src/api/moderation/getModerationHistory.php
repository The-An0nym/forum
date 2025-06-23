<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/require/moderationHistory.php' ;

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
    $page = 0;
    if (isset($_GET["p"])) {
        $page = (int)$_GET["p"];
    }

    $report = false;
    if(isset($_GET["r"])) {
        if($_GET["r"] === "1") {
            $report = true;
        }
    }


    $conn = getConn();
        
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT u.clearance FROM users u 
            WHERE u.user_id = '$user_id'
            LIMIT 1";

    $result = $conn->query($sql);

    $clearance = $result->fetch_assoc()["clearance"];

    if($clearance < 1) {
        echo "Insufficient authority";
        die();
    }

    $data = getHistoryHTML($report, $page, $clearance);
    echo trim($data);
} else {
    echo "Please login";
}