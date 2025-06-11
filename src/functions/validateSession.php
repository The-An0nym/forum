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

if(isset($_SESSION['user_id'], $_SESSION['session_id'])) {
    $session_id = $_SESSION['session_id'];

    // Don't search by session_id ?
    $sql = "SELECT ip, user_agent, user_id, datetime FROM sessions WHERE session_id='$session_id'";
    // Compare each character instead
    // OR hash_equals($string1, $string2) : Bool
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $db_ip = $row["ip"];
        $db_user_agent = $row["user_agent"];
        $db_user_id = $row["user_id"];
        $db_datetime = $row["datetime"];

        if($_SESSION['user_id'] === $db_user_id && $_SERVER['REMOTE_ADDR'] === $db_ip && $_SERVER['HTTP_USER_AGENT'] === $db_user_agent) {
            $diff = time() - strtotime($db_datetime);
            // Sessions is valid for max. 20h
            if($diff <= 60 * 60 * 20) {
                $dtime = date('Y-m-d H:i:s');
                $sql = "UPDATE sessions SET datetime='$dtime' WHERE session_id = '$session_id'";
                if ($conn->query($sql) === TRUE) {
                    return true;
                }
            } else {
                include('functions/clearSession.php');
            }
        }
    }
}

if(isset($_SESSION["user_id"])) {
    unset($_SESSION['user_id']);
}

if(isset($_SESSION["session_id"])) {
    unset($_SESSION['session_id']);
}

return false;
