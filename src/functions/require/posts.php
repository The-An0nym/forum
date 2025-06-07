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

    // Total posts
    $sql = "SELECT COUNT(*) AS total_posts
                FROM posts p
                JOIN threads t ON t.id = p.thread_id
                WHERE t.slug = '$slug'";
    
    $result = $conn->query($sql);
    $total_posts = $result->fetch_assoc()["total_posts"];

    if(isset($_SESSION["user_id"])) {
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT clearance FROM users WHERE user_id = '$user_id'";
        $result = $conn->query($sql);
        $myClearance = $result->fetch_assoc()["clearance"];
    } else {
        $myClearance = 0;
    }

    $sql = "SELECT 
                u.username, 
                u.image_dir,
                u.posts,
                p.post_id, 
                p.user_id, 
                p.content, 
                p.created, 
                p.edited,
                u.clearance
            FROM 
                posts p
            LEFT JOIN 
                users u ON u.user_id = p.user_id
            JOIN 
                threads t ON t.id = p.thread_id
            WHERE 
                t.slug = '$slug'
            AND 
                p.deleted = 0
            ORDER BY 
                p.created ASC
            LIMIT 20 OFFSET $page";
    
    $result = $conn->query($sql);

    $data = [];
    $data[] = $total_posts;
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if($row["clearance"] < $myClearance) {
                $row["clearance"] = 1;
            } else {
                $row["clearance"] = 0;
            }
            $data[] = $row;
        }
        return $data;
    } else {
        return [];
    }
}

function getPathNames(string $slug) {
    $path = $_SERVER['DOCUMENT_ROOT'];
    include $path . '/functions/.connect.php' ;

    // Get connection
    $conn = getConn();

    // Check connection
    if ($conn->connect_error) {
        return [];
    }

    // Category
    $sql = "SELECT c.slug AS c_slug, c.name AS c_name, t.name AS t_name
    FROM categories c
    JOIN threads t ON t.category_id = c.id
    WHERE t.slug = '$slug'";

    $result = $conn->query($sql);
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        return [["topic/" . $row["c_slug"], $row["c_name"]], ["thread/" . $slug, $row["t_name"]]];
    } 
    return [];
}