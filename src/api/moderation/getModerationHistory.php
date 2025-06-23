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
            $reports = true;
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

    $rows = getHistoryHTML($report, $page, $clearance);

    $data = [];

    // Next page?
    if($rows < 50) {
        $data[] = false;
    } else {
        $data[] = true;
    }

    if($rows > 1) {
        // output data of each thread
        foreach($rows as $row) {
            $t = new stdClass();
            $t->sender_username = $row["sender_username"];
            $t->sender_handle = $row["sender_handle"];
            $t->culp_username = $row["culp_username"];
            $t->culp_handle = $row["culp_handle"];
            $t->culp_clearance = $row["culp_clearance"];
            $t->culp_id = $row["culp_id"];
            $t->mod_id = $row["mod_id"];
            $t->id = $row["id"];
            $t->type = $row["type"];
            $t->judgement = $row["judgement"];
            $t->summary = $row["summary"];
            $t->reason = $row["reason"];
            $t->message = $row["message"];
            $t->created = $row["created"];
            $data[] = $t;
        }
    }

} else {
    echo "Please login";
}