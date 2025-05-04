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

if(include($path . '/functions/validateSession.php')) {
    $json_params = file_get_contents("php://input");

    if (strlen($json_params) > 0 && strlen($cont) <= 2000 && json_validate($json_params)) {
        $decoded_params = json_decode($json_params);

        // Escaping content and trimming whitespace
        $cont = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($decoded_params->c)); // idk about mysql_real_escape_string ??
            
        if(strlen($cont) !== 0 && strlen($cont) <= 2000) {
            $user_id = $_SESSION["user_id"];
            $post_id = $decoded_params->i;
            $sql = "UPDATE posts
                    SET content = '$cont', edited = '1'
                    WHERE post_id = '$post_id' AND user_id = '$user_id'";
            if ($conn->query($sql) === FALSE) {
                echo "An error has occured [SE0]";
            } 
        } else if(strlen($cont) === 0) {
            echo "No content";
        } else if(strlen($cont) > 2000) {
            echo "2000 character limit surpassed";
        } else {
            echo "ERROR: SE1";
        }

    } else {
        echo "ERROR: SE2";
    }

} else {
    echo "Please Login to edit posts";
}

$conn->close();
?>