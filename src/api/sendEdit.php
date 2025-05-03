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

    if (strlen($json_params) > 0 && json_validate($json_params)) {
        $decoded_params = json_decode($json_params);

        // idk about mysql_real_escape_string ??
        $cont = trim(htmlspecialchars($decoded_params->c), "\u{0009}\u{000a}\u{000b}\u{000c}\u{000d}\u{0020}\u{00a0}\u{0085}\u{1680}\u{180e}\u{2000}\u{2001}\u{2002}\u{2003}\u{2004}\u{2005}\u{2006}\u{2007}\u{2008}\u{2009}\u{200a}\u{200b}\u{2028}\u{2029}\u{202f}\u{205f}\u{3000}\u{feff}"); // idk about mysql_real_escape_string ??
            
        if(strlen($cont) !== 0 && strlen($cont) <= 2000) {
            $user_id = $_SESSION["user_id"];
            $post_id = $_POST["i"];
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
        }
    } else {
        echo "ERROR: SE1"
    }
} else {
    echo "Please Login to edit posts";
}

$conn->close();
?>