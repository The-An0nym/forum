<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/validateSession.php';
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

if(validateSession()) {
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

    $params = [];
    if(isset($_GET["c"])) {
        $params["culp_handle"] = $_GET["c"];
    }
    if(isset($_GET["s"])) {
        $params["sender_handle"] = $_GET["s"];
    }
    if(isset($_GET["t"])) {
        $params["type"] = (int)$_GET["t"];
    }
    if(isset($_GET["i"])) {
        $params["id"] = $_GET["i"];
    }
    if(isset($_GET["rev"])) {
        $params["reverse"] = $_GET["rev"];
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

    $data = getHistoryHTML($report, $page, $clearance, $params, true);

    echo trim($data);
} else {
    echo "Please login";
}