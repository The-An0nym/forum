<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;
require_once $path . '/functions/validateSession.php';
require_once $path . '/functions/errors.php' ;
require_once $path . '/functions/require/posts.php' ;

echo response();

function response() : string {
    // Get connection
    $conn = getConn();

    if(!session_id()) {
        session_start();
    }

    if(!validateSession()) { 
        return jsonErr("login");
    }

    $json_params = file_get_contents("php://input");

    if (strlen($json_params) === 0 || !json_validate($json_params)) {
        return jsonErr("args");
    }
    
    $json_obj = json_decode($json_params);

    if(!isset($json_obj->s, $json_obj->c)) {
        return jsonErr("args");
    }

    $slug = $json_obj->s;

    $sql = "SELECT `id` FROM `threads` WHERE `slug` = '$slug' AND `deleted` = 0";
        
    $result = $conn->query($sql);
    if ($result->num_rows === 0) {
        return jsonErr("404thrd");
    }
    
    $thread_id = $result->fetch_assoc()["id"];

    // Escaping content and trimming whitespace
    $cont = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', $json_obj->c); // aggressive trim

    if(strlen($cont) === 0) {
        return jsonErr("contMin");
    } else if(strlen($cont) > 2000) {
        return jsonErr("contMax");
    }

    $user_id = $_SESSION["user_id"];

    // Rate limiting
    $sql = "SELECT COUNT(*) AS `cnt` FROM `posts` WHERE `user_id` = '$user_id' AND (`created` > DATE_SUB(CONVERT_TZ(NOW(),'SYSTEM','+00:00'), INTERVAL 15 SECOND) OR (`edited_datetime` > DATE_SUB(CONVERT_TZ(NOW(),'SYSTEM','+00:00'), INTERVAL 15 SECOND))";
    $result = $conn->query($sql);
    $cnt = $result->fetch_assoc()["cnt"];
    if($cnt > 0) {
        return jsonErr("postRate");
    }

    $dtime = date('Y-m-d H:i:s');
    $post_id = uniqid(rand(), true);
    $sql = "INSERT INTO `posts` (`user_id`, `post_id`, `content`, `created`, `thread_id`)
    VALUES ('$user_id', '$post_id', '$cont', '$dtime', '$thread_id')";
    if ($conn->query($sql) === FALSE) {
        return jsonErr("", "[SP0]");
    }

    // Increment post count of user
    $sql = "UPDATE `users` SET `posts` = posts +1 WHERE `user_id` = '$user_id'";
    if ($conn->query($sql) === FALSE) {
        return jsonErr("", "[SP1]");
    }

    // Increment post count of category and thread
    $sql = "UPDATE categories c
            INNER JOIN threads t ON t.category_id = c.id
            SET c.posts = c.posts +1, t.posts = t.posts +1 
            WHERE t.id = '$thread_id'";
    if ($conn->query($sql) === FALSE) {
        return jsonErr("", "[SP2]");
    }

    // Send notification
    $sql = "SELECT `user_id` FROM `subscribed` WHERE `thread_id` = '$thread_id' AND `subscribed` = 1";
    $result = $conn->query($sql);

    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $rec_id = $row["user_id"];
            if($rec_id === $user_id) continue;

            $notif_id = uniqid(rand(), true);
            $datetime = date("Y-m-d H:i:s");

            $sql = "INSERT INTO
                        `notifications` 
                        (`notification_id`, `sender_id`, `receiver_id`, `type`, `thread_id`, `assoc_id`, `datetime`) 
                    VALUES
                        ('$notif_id', '$user_id', '$rec_id', 0, '$thread_id', '$post_id', '$datetime')";
            if($conn->query($sql) === FALSE) {
                return jsonErr("", "SP3");
            }
        }
    }

    return getPostsJson($slug, -1);
}