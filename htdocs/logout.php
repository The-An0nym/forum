<?php

// CONNECT
$configs = include('functions/.config.php');
extract($configs);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

session_start();

$res = include('functions/clearSession.php');
if($res === "PASS") {
    header('Location: https://quir.free.nf');
} else {
    echo $res;
}
?>