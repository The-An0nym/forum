<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/validateSession.php';
include($path . '/functions/slug.php');

// Get connection
$conn = getConn();

if(!session_id()) {
  session_start();
}

if(validateSession()) {

    $json_params = file_get_contents("php://input");

    if (strlen($json_params) > 0 && json_validate($json_params)) {
        $decoded_params = json_decode($json_params);

        $slug = $decoded_params->s;

        $sql = "SELECT id FROM categories WHERE slug = '$slug'";

        $result = $conn->query($sql);
        if ($result->num_rows == 1) {
            $thread_slug = generateSlug($decoded_params->t);

            // Escaping content and trimming whitespace
            $threadName = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($decoded_params->t)); // idk about mysql_real_escape_string ??
            $category_id = $result->fetch_assoc()["id"];
            // Escaping content and trimming whitespace
            $cont = nl2br(preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($decoded_params->c))); // mysql_real_escape_string ??

            if(strlen($cont) !== 0 && strlen($cont) <= 2000 && strlen($threadName) <= 64 && strlen($threadName) >= 8) {               
                $user_id = $_SESSION["user_id"];

                // Create Thread
                $dtime = date('Y-m-d H:i:s');
                $thread_id = uniqid(rand(), true);
                $sql = "INSERT INTO threads (name, slug, id, category_id, created, user_id, posts)
                VALUES ('$threadName', '$thread_slug', '$thread_id', '$category_id', '$dtime', '$user_id', 1)";
                if ($conn->query($sql) === FALSE) {
                    echo "An error has occured [CT0]";
                }

                // Create Post
                $post_id = uniqid(rand(), true);
                $sql = "INSERT INTO posts (user_id, post_id, content, created, edited, thread_id)
                VALUES ('$user_id', '$post_id', '$cont', '$dtime', 'false', '$thread_id')";
                if ($conn->query($sql) === FALSE) {
                    echo "An error has occured [CT1]";
                }

                // Increment post and thread count of user
                $sql = "UPDATE users SET posts = posts +1, threads = threads +1 WHERE user_id = '$user_id'";
                if ($conn->query($sql) === FALSE) {
                    echo "An error has occured [CT2]";
                }

                // Increment post count of category
                $sql = "UPDATE categories
                        SET posts = posts +1, threads = threads +1 
                        WHERE id = '$category_id'";
                if ($conn->query($sql) === FALSE) {
                    echo "An error has occured [CT3]";
                }

            } else if(strlen($cont) === 0) {
                echo "No content";
            } else if(strlen($cont) > 2000) {
                echo "2000 character limit surpassed";
            } else if(strlen($threadName) > 64) {
                echo "Max. 64 chars allowed for thread names";
            } else if(strlen($threadName) < 8) {
                echo "At least 8 chars are needed for a thread name";
            } else {
                echo "ERROR CT4";
            }
        } else {
            echo "ERROR: CT5";
        }

    } else {
        echo "ERROR: Invalid or missing arguments.";
    }

} else {
    echo "Please Login to post";
}