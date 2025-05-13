<?php
function getPosts(string $slug, int $page) :array {   
    $path = $_SERVER['DOCUMENT_ROOT'];
    include $path . '/functions/.connect.php' ;

    // Get connection
    $conn = getConn();

    // Check connection
    if ($conn->connect_error) {
        return [];
    }

    $sql = "SELECT COUNT(*) AS total_posts
                FROM posts p
                JOIN threads t ON t.id = p.thread_id
                WHERE t.slug = '$slug'";
    
    $result = $conn->query($sql);
    $total_posts = $result->fetch_assoc()["total_posts"];

    $sql = "SELECT 
                u.username, 
                u.image_dir,
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

    $data = [];
    $data[] = $total_posts;
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    } else {
        return [];
    }

    $conn->close();
}