<?php

function connection() {
    $path = $_SERVER['DOCUMENT_ROOT'];
    include $path . '/functions/.connect.php' ;

    // Get connection
    $conn = getConn();

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

function syncAll() {
    $conn = connection();

    // Update threads
    $sql = "UPDATE threads t
            JOIN (
                SELECT thread_id, COUNT(*) AS cnt
                FROM posts
                WHERE deleted = 0
                GROUP BY thread_id
            ) p ON t.id = p.thread_id
            SET t.posts = p.cnt";
    if ($conn->query($sql) === FALSE) {
        echo "An error has occured while syncing threads";
    }

    // Update category
    $sql = "UPDATE categories c
            JOIN (
                SELECT category_id, COUNT(*) AS cnt, SUM(posts) AS sum
                FROM threads
                WHERE deleted = 0
                GROUP BY category_id
            ) t ON c.id = t.category_id
            SET c.threads = t.cnt, c.posts = t.sum";
    if ($conn->query($sql) === FALSE) {
        echo "An error has occured while syncing categories";
    }

    // Update user post count
    $sql = "UPDATE users u
            JOIN (
                SELECT user_id, COUNT(*) AS cnt
                FROM posts
                WHERE deleted = 0
                GROUP BY user_id
            ) p ON u.user_id = p.user_id
            JOIN (
                SELECT user_id, COUNT(*) AS cnt
                FROM threads
                WHERE deleted = 0
                GROUP BY user_id
            ) t ON u.user_id = t.user_id
            SET u.posts = p.cnt, u.threads = t.cnt";
    if ($conn->query($sql) === FALSE) {
        echo "An error has occured while syncing user post count";
    }
}

function countForPost($id, bool $rest) {
    $conn = connection();

    if($rest) {
        $op = '+ 1';
    } else {
        $op = '- 1';
    }

    // User
    $sql = "UPDATE users u, threads t, categories c, posts p SET
                u.posts = u.posts $op 1,
                t.posts = t.posts $op 1,
                c.posts = c.posts $op 1
            WHERE p.post_id = '$id' AND p.user_id = u.user_id AND t.id = p.thread_id AND c.id = t.category_id";
    
    if($conn->query($sql) === FALSE) {
        echo "An error has occured while updating the post counts";
    }
}

function countForThread($id, bool $rest) {
    $conn = connection();

    if($rest) {
        $op = '+';
    } else {
        $op = '-';
    }

    // Thread and post counts for categories and sender
    $sql = "UPDATE users u, threads t, categories c SET
                c.threads = c.threads $op 1,
                c.posts = c.posts $op t.posts,
                u.threads = u.threads $op 1
            WHERE t.id = '$id' AND t.category_id = c.id AND t.user_id = u.user_id";
    if($conn->query($sql) === FALSE) {
        echo "An error has occured while updating the thread count";
    }

    // All user post counts
    $sql = "UPDATE users u
                JOIN (
                    SELECT 
                        p.user_id, COUNT(*) AS psts
                    FROM 
                        posts p
                    WHERE deleted = 0 AND p.thread_id = '$id'
                    GROUP BY p.user_id
                ) p
            ON u.user_id = p.user_id
            SET u.posts = u.posts $op p.psts";
    if($conn->query($sql) === FALSE) {
        echo "An error has occured while updating the users' post count";
    }
}

function countForUser($id, bool $rest, bool $threads) {
    $conn = connection();

    if($rest) {
        $op = '+';
    } else {
        $op = '-';
    }

    // Thread post count
    $sql = "UPDATE threads t
            JOIN (
                SELECT p.thread_id, COUNT(*) AS psts FROM posts p
                WHERE p.user_id = '$id' AND p.deleted = p.deleted & ~7
                GROUP BY p.thread_id
                ) p
            ON t.id = p.thread_id
            SET t.posts = t.posts $op p.psts";
    if($conn->query($sql) === FALSE) {
        echo "An error has occured while updating the threads' post count";
    }

    // Categories post count
    $sql = "UPDATE 
                categories c
            JOIN (
                SELECT t.category_id, COUNT(*) AS psts FROM posts p
                JOIN threads t ON t.id = p.thread_id
                WHERE p.user_id = '$id' AND p.deleted = p.deleted & ~7
                GROUP BY t.category_id
                ) p
            ON c.id = p.category_id
            SET c.posts = c.posts $op p.psts"
    if($conn->query($sql) === FALSE) {
        echo "An error has occured while updating the categories' post count";
    }


    if(!$threads) {
        return;
    }

    $sql = "SELECT id FROM threads WHERE user_id = '$id'";

    $result = $conn->query($sql);
    if($result->num_rows === 0) {
        return;
    }

    // Update counts for each deleted or restored thread
    while($row = $result->fetch_assoc()) {
        countForThread($row["id"], $rest);
    }
}