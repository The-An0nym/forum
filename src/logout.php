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

$res = include($path . '/functions/clearSession.php');
if($res === "PASS") {
    header('Location: https://quir.free.nf');
} else {
    echo $res;
}
?>