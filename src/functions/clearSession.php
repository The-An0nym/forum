<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/connect.php' ;

// Get Connection
$conn = getConn();

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if(!session_id()) {
    session_start();
}

include('deleteExpiredSessions.php');

if(!isset($_SESSION['session_id'])) {
    return "No session_id";
    exit;
}

$session_id = $_SESSION['session_id'];
$sql = "DELETE FROM sessions WHERE session_id='$session_id'";
$result = $conn->query($sql);

if ($conn->query($sql) === TRUE) {
    unset($_SESSION['user_id']);
    unset($_SESSION['session_id']);
    return "";
} else {
  return "ERROR CS0";
}

return "ERROR CS1";