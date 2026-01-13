<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;
require_once $path . '/functions/validateSession.php';
require_once $path . '/functions/slug.php' ;
require_once $path . '/functions/errors.php' ;
require_once $path . '/functions/require/threads.php' ;

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

    if(!isset($json_obj->s, $json_obj->t, $json_obj->c)) {
        return jsonErr("args");
    }

    $user_id = $_SESSION["user_id"];

    // Rate limiting
    $sql = "SELECT COUNT(*) AS `cnt` FROM `posts` WHERE `user_id` = '$user_id' AND `created` > DATE_SUB(CONVERT_TZ(NOW(),'SYSTEM','+00:00'), INTERVAL 15 SECOND)";
    $result = $conn->query($sql);
    $cnt = $result->fetch_assoc()["cnt"];
    if($cnt > 0) {
        return jsonErr("thrdRate");
    }

    $slug = $json_obj->s;

    $sql = "SELECT `id` FROM `categories` WHERE `slug` = '$slug'";

    $result = $conn->query($sql);
    if ($result->num_rows !== 1) {
        return jsonErr("404cat");
    }
    
    $thread_slug = generateSlug($json_obj->t);

    // Escaping content and trimming whitespace
    $threadName = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($json_obj->t)); // idk about mysql_real_escape_string ??
    $category_id = $result->fetch_assoc()["id"];
    // Escaping content and trimming whitespace
    $cont = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($json_obj->c)); // aggressive trim

    if(strlen($cont) === 0) {
        return jsonErr("contMin");
    } else if(strlen($cont) > 2000) {
        return jsonErr("contMax");
    } else if(strlen($threadName) > 64) {
        return jsonErr("thrdMax");
    } else if(strlen($threadName) < 8) {
        return jsonErr("thrdMin");
    }

    // Create Thread
    $dtime = date('Y-m-d H:i:s');
    $thread_id = uniqid(rand(), true);
    $sql = "INSERT INTO `threads` (`name`, `slug`, `id`, `category_id`, `created`, `user_id`, `posts`)
    VALUES ('$threadName', '$thread_slug', '$thread_id', '$category_id', '$dtime', '$user_id', 1)";
    if ($conn->query($sql) === FALSE) {
        return jsonErr("", "[CT0]");
    }

    // Create Post
    $post_id = uniqid(rand(), true);
    $sql = "INSERT INTO `posts` (`user_id`, `post_id`, `content`, `created`, `thread_id`)
    VALUES ('$user_id', '$post_id', '$cont', '$dtime', '$thread_id')";
    if ($conn->query($sql) === FALSE) {
        return jsonErr("", "[CT1]");
    }

    // Increment post and thread count of user and category
    $sql = "UPDATE `users` SET `posts` = posts +1, `threads` = threads +1 WHERE `user_id` = '$user_id'";
    if ($conn->query($sql) === FALSE) {
        return jsonErr("", "[CT2]");
    }

    // Increment post count of category
    $sql = "UPDATE `categories` SET `posts` = posts + 1, `threads` = threads + 1 WHERE `id` = '$category_id'";
    if ($conn->query($sql) === FALSE) {
        return jsonErr("", "[CT3]");
    }

    // Appending newly generated slug name
    $threadsDec = json_decode(getThreadsJson($slug, 1), TRUE);
	$threadsDec["data"]["slug"] = $thread_slug;
    $threadsJson = json_encode($threadsDec);

    return $threadsJson;
}