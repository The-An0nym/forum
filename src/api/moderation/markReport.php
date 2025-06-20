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
    if (isset($_GET["r"], $_GET["i"])) {

        $id = $_GET["i"];
        $as = (int)$_GET["r"];

        if($as !== 0 && $as !== 1) {
            echo "'AS' not valid";
            die();
        }

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

        if($clearance === 1) {
            $sql = "SELECT mp.mod_id AS post FROM mod_history mh
                    LEFT JOIN mod_history_posts mp ON mp.mod_id = mh.mod_id
                    WHERE mh.mod_id = '$id' LIMIT 1";
        } else if($clearance === 2) {
            $sql = "SELECT mp.mod_id AS post, mt.mod_id AS thread FROM mod_history mh
                    LEFT JOIN mod_history_posts mp ON mp.mod_id = mh.mod_id
                    LEFT JOIN mod_history_threads mt ON mt.mod_id = mh.mod_id
                    WHERE mh.mod_id = '$id' LIMIT 1";
        } else if($clearance > 2) {
            $sql = "SELECT mp.mod_id AS post, mt.mod_id AS thread, mu.mod_id AS user FROM mod_history mh
                    LEFT JOIN mod_history_posts mp ON mp.mod_id = mh.mod_id
                    LEFT JOIN mod_history_threads mt ON mt.mod_id = mh.mod_id
                    LEFT JOIN mod_history_users mu ON mu.mod_id = mh.mod_id
                    WHERE mh.mod_id = '$id' LIMIT 1";
        }
        
        $result = $conn->query($sql);

        if($result->num_rows !== 1) {
            echo "An error has occured MR1";
            die();
        }

        $row = $result->fetch_assoc();

        if(isset($row["post"]) || isset($row["thread"]) || isset($row["user"])) {
            // Update
            $sql = "UPDATE mod_history SET judgement = $as WHERE mod_id = '$id'";
            if ($conn->query($sql) === FALSE) {
                echo "An error has occured [MR2]";
            } 
        }    
        
    } else {
        echo "An error has occured MR3";
    }

} else {
    echo "Please login";
}