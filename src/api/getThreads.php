<?php

$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;

// Get connection
$conn = getConn();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_GET['s'])) {
    $slug = $_GET['s'];
    if(isset($_GET['p'])) {
        $page = $_GET['p'];
    } else {
        $page = 0;
    }
    
    if($slug != "") {
        $sql = "SELECT 
                    t.name, 
                    t.slug,
                    t.created, 
                    t.posts,
                    u.username AS lastUser,
                    lp.created AS lastPost
                FROM 
                    threads t
                JOIN categories c ON c.id = t.category_id
                LEFT JOIN (
                    SELECT 
                        p1.thread_id, 
                        p1.user_id,
                        p1.created
                    FROM 
                        posts p1
                    INNER JOIN (
                        SELECT 
                            thread_id, 
                            MAX(created) AS maxCreated
                        FROM 
                            posts
                        GROUP BY 
                            thread_id
                    ) p2 ON p1.thread_id = p2.thread_id AND p1.created = p2.maxCreated
                ) lp ON t.id = lp.thread_id
                LEFT JOIN (
                    SELECT username, user_id FROM users
                ) u ON u.user_id = lp.user_id
                WHERE 
                    c.slug = '$slug'
                ORDER BY 
                    p.created ASC
                LIMIT 10 OFFSET '$page'";

        $result = $conn->query($sql);

        $data = [];
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $post = new stdClass();
                $post->name = $row["name"];
                $post->slug = $row["slug"];
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