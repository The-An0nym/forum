<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path . '/functions/.config.php');

// Get connection
$conn = getConn();

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if(!session_id()) {
    session_start();
}

$user_id = $_SESSION['user_id'];

$delArr = array();

// Could be optimized in the future by ordering DESC or even only picking the ones that have expired on the db.
$sql = "SELECT session_id, datetime FROM sessions WHERE user_id='$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $dtime = $row['datetime'];
        $diff = time() - strtotime($dtime);
        if($diff > 60 * 60 * 20) {
                $delArr[] = $row['session_id'];
            }
    }
} else {
    return;
}

foreach ($delArr as $sess) {
    $sql = "DELETE FROM sessions WHERE session_id='$sess'";
    $result = $conn->query($sql);

    if ($conn->query($sql) === TRUE) {
    } else {
        echo "An error has occured";
    }
}

?>