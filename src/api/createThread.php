<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include($path . '/functions/slugify.php');

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

        $sql = "SELECT id FROM categories WHERE slug = '$slug'";

        $result = $conn->query($sql);
        if ($result->num_rows == 1) {
            $threadName = trim(htmlspecialchars($slug = $decoded_params->t), "\u{0009}\u{000a}\u{000b}\u{000c}\u{000d}\u{0020}\u{00a0}\u{0085}\u{1680}\u{180e}\u{2000}\u{2001}\u{2002}\u{2003}\u{2004}\u{2005}\u{2006}\u{2007}\u{2008}\u{2009}\u{200a}\u{200b}\u{2028}\u{2029}\u{202f}\u{205f}\u{3000}\u{feff}"); // idk about mysql_real_escape_string ??
            $category_id = $result->fetch_assoc()["id"];
            $cont = trim(htmlspecialchars($slug = $decoded_params->p), "\u{0009}\u{000a}\u{000b}\u{000c}\u{000d}\u{0020}\u{00a0}\u{0085}\u{1680}\u{180e}\u{2000}\u{2001}\u{2002}\u{2003}\u{2004}\u{2005}\u{2006}\u{2007}\u{2008}\u{2009}\u{200a}\u{200b}\u{2028}\u{2029}\u{202f}\u{205f}\u{3000}\u{feff}"); // idk about mysql_real_escape_string ??

            if(strlen($cont) !== 0 && strlen($cont) <= 2000 && strlen($threadName) <= 64 && strlen($threadName) >= 8) {
                // Create Thread
                $dtime = date('Y-m-d H:i:s');
                $thread_id = uniqid(rand(), true);
                $thread_slug = slugify($threadName);
                $sql = "INSERT INTO threads (name, slug, id, category_id, created, posts)
                VALUES ('$threadName', '$thread_slug', '$thread_id', '$category_id', '$dtime', 1)";
                if ($conn->query($sql) === FALSE) {
                    echo "An error has occured [CT0]";
                }

                // Create Post
                $post_id = uniqid(rand(), true);
                $user_id = $_SESSION["user_id"];
                $sql = "INSERT INTO posts (user_id, post_id, content, created, edited, thread_id)
                VALUES ('$user_id', '$post_id', '$cont', '$dtime', 'false', '$thread_id')";
                if ($conn->query($sql) === FALSE) {
                    echo "An error has occured [CT1]";
                }

                // Increment post count of user
                $sql = "UPDATE users SET posts = posts +1 WHERE user_id = '$user_id'";
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
            }
        } else {
            echo "ERROR: CT4";
        }

    } else {
        echo "ERROR: Invalid or missing arguments.";
    }

} else {
    echo "Please Login to post";
}

$conn->close();
?>