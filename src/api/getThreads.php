<?php

$path = $_SERVER['DOCUMENT_ROOT'];
include($path . '/functions/.config.php');

// Get connection
$conn = getConn();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_GET['n'])) {
    $category = str_replace("-", " ", $_GET['n']);
    if($category != "") {
        $sql = "SELECT 
                t.name, 
                t.created, 
                t.posts,
                u.username AS lastUser,
                lp.created AS lastPost
            FROM 
                threads t
            LEFT JOIN (
                SELECT 
                    p1.thread, 
                    p1.user_id,
                    p1.created
                FROM 
                    posts p1
                INNER JOIN (
                    SELECT 
                        thread, 
                        MAX(created) AS maxCreated
                    FROM 
                        posts
                    GROUP BY 
                        thread
                ) p2 ON p1.thread = p2.thread AND p1.created = p2.maxCreated
            ) lp ON t.name = lp.thread
            INNER JOIN (
                    SELECT username, user_id FROM users
                ) u ON u.user_id = lp.user_id
                WHERE t.category = '$category'";
        $result = $conn->query($sql);

        $data = [];
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $post = new stdClass();
                $post->name = $row["name"];
                $post->created = $row["created"];
                $post->postCount = $row["posts"];
                $post->lastUser = $row["lastUser"];
                $post->lastPost = $row["lastPost"];
                $data[] = $post;
            }

            $dataJSON = json_encode($data);
            echo $dataJSON;
        } else {
        echo "ERROR: Failed to load";
        }
    } else {
        echo "ERROR: Invalid or missing argument";
    }
} else {
    echo "ERROR: Invalid or missing arguments";
}

$conn->close();
?>   