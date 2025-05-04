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

        $slug = $decoded_params->s;
        
        $sql = "SELECT id FROM threads WHERE slug = '$slug'";
        
        $result = $conn->query($sql);
        if ($result->num_rows == 1) {
            $thread_id = $result->fetch_assoc()["id"];

            // Escaping content and trimming whitespace
            $cont = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($decoded_params->c)); // idk about mysql_real_escape_string ??
            
            if(strlen($cont) !== 0 && strlen($cont) <= 2000) {
                $dtime = date('Y-m-d H:i:s');
                $post_id = uniqid(rand(), true);
                $user_id = $_SESSION["user_id"];
                $sql = "INSERT INTO posts (user_id, post_id, content, created, edited, thread_id)
                VALUES ('$user_id', '$post_id', '$cont', '$dtime', 'false', '$thread_id')";
                if ($conn->query($sql) === FALSE) {
                    echo "An error has occured [SP0]";
                }

                // Increment post count of user
                $sql = "UPDATE users SET posts = posts +1 WHERE user_id = '$user_id'";
                if ($conn->query($sql) === FALSE) {
                    echo "An error has occured [SP1]";
                }

                // Increment post count of category and thread
                $sql = "UPDATE categories c
                        INNER JOIN threads t ON t.category_id = c.id
                        SET c.posts = c.posts +1, t.posts = t.posts +1 
                        WHERE t.id = '$thread_id'";
                if ($conn->query($sql) === FALSE) {
                    echo "An error has occured [SP2]";
                }

            } else {
                echo "An error has occured [SP0]";
            }

        } else if(strlen($cont) === 0) {
            echo "No content";
        } else if(strlen($cont) > 2000) {
            echo "2000 character limit surpassed";
        }

    } else {
        echo "ERROR: Invalid or missing arguments.";
    }

} else {
    echo "Please Login to post";
}

$conn->close();
?>