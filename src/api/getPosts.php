<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;

// Get connection
$conn = getConn();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_GET['s'], $_GET['p'])) {
    $slug = $_GET['s'];
    $page = $_GET['p'] * 20;

    $sql = "SELECT COUNT(*) AS total_posts
                FROM posts p
                JOIN threads t ON t.id = p.thread_id
                WHERE t.slug = '$slug'";
    $result = $conn->query($sql);
    $total_posts = $result->fetch_assoc()["total_posts"];

    $sql = "SELECT 
                u.username, 
                u.image_id,
                u.posts,
                p.post_id, 
                p.user_id, 
                p.content, 
                p.created, 
                p.edited
            FROM 
                posts p
            LEFT JOIN 
                users u ON u.user_id = p.user_id
            JOIN 
                threads t ON t.id = p.thread_id
            WHERE 
                t.slug = '$slug'
            ORDER BY 
                p.created ASC
            LIMIT 20 OFFSET $page";
    $result = $conn->query($sql);

    session_start();

    $data = [];
    $data[] = $total_posts;
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $post = new stdClass();
            $post->username = $row["username"];
            $post->imageSrc = $row["image_id"];
            $post->userPostCount = $row["posts"];
            $post->id = $row["post_id"];
            $post->content = $row["content"];
            $post->created = $row["created"];
            $post->edited = $row["edited"];
            if(isset($_SESSION["user_id"])) {
            if($row["user_id"] == $_SESSION["user_id"]) {
                $post->editable = true;
            } else {
                $post->editable = false;
            }} else {
                $post->editable = false;
            }
            $data[] = $post;
        }

        $dataJSON = json_encode($data);
        echo $dataJSON;

    } else {
        echo "ERROR: Failed to load";
    }

} else {
    echo "No thread found...";
    die();
}

$conn->close();