<?php
function syncAll() : array {
    $path = $_SERVER['DOCUMENT_ROOT'];
    require_once $path . '/functions/.connect.php' ;
    $conn = getConn();

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
        return ["", "SC0"];
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
        return ["", "SC1"];
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
        return ["", "SC2"];
    }

    return ["pass"];
}

function countForPost($id, bool $rest) : array {
    $path = $_SERVER['DOCUMENT_ROOT'];
    require_once $path . '/functions/.connect.php' ;
    $conn = getConn();

    if($rest) {
        $op = '+';
    } else {
        $op = '-';
    }

    // User
    $sql = "UPDATE users u, threads t, categories c, posts p SET
                u.posts = u.posts $op 1,
                t.posts = t.posts $op 1,
                c.posts = c.posts $op 1
            WHERE p.post_id = '$id' AND p.user_id = u.user_id AND t.id = p.thread_id AND c.id = t.category_id";
    
    if($conn->query($sql) === FALSE) {
        return ["", "SC3"];
    }

    $err = checkEmptyThreads();
    if($err[0] !== "pass") {
        return $err;
    }

    return ["pass"];
}

function countForThread($id, bool $rest) : array {
    $path = $_SERVER['DOCUMENT_ROOT'];
    require_once $path . '/functions/.connect.php' ;
    $conn = getConn();

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
        return ["", "SC4"];
    }

    // All user post counts
    $sql = "UPDATE users u
                JOIN (
                    SELECT 
                        p.user_id, COUNT(*) AS psts
                    FROM 
                        posts p
                    WHERE deleted = deleted & ~11 AND p.thread_id = '$id'
                    GROUP BY p.user_id
                ) p
            ON u.user_id = p.user_id
            SET u.posts = u.posts $op p.psts";
    if($conn->query($sql) === FALSE) {
        return ["", "SC5"];
    }

    return ["pass"];
}

// Note: This function should only be executed when the posts/threads are deleted
function countForUser($id, bool $rest, bool $threads) : array {
    $path = $_SERVER['DOCUMENT_ROOT'];
    require_once $path . '/functions/.connect.php' ;
    $conn = getConn();

    if($rest) {
        $op = '+';
    } else {
        $op = '-';
    }

    // TODO cannot delete user -> There is a bug overcounting user posts
    // 8 -> Only user deleted
    // Thread post count
    $sql = "UPDATE threads t
            JOIN (
                SELECT p.thread_id, COUNT(*) AS psts FROM posts p
                WHERE p.user_id = '$id' AND p.deleted = 8
                GROUP BY p.thread_id
                ) p
            ON t.id = p.thread_id
            SET t.posts = t.posts $op p.psts";

    // As this already gets taken care of in the deleteThreads function
    if($threads) {
        $sql .= "\nWHERE t.user_id != '$id'";
    }
    
    if($conn->query($sql) === FALSE) {
        return ["", "SC6"];
    }

    // Categories post count
    $sql = "UPDATE 
                categories c
            JOIN (
                SELECT t.category_id, COUNT(*) AS psts FROM posts p
                JOIN threads t ON t.id = p.thread_id
                WHERE p.user_id = '$id' AND p.deleted = 8";

    // As this already gets taken care of in the deleteThreads function
    if($threads) {
        $sql .= " AND t.user_id != '$id'";
    }
    
    $sql .= "\nGROUP BY t.category_id
                ) p
            ON c.id = p.category_id
            SET c.posts = c.posts $op p.psts";
    if($conn->query($sql) === FALSE) {
        return ["", "SC7"];
    }
    

    // User post count
    $sql = "UPDATE 
                users u
            JOIN (
                SELECT p.user_id, COUNT(*) AS psts FROM posts p";

    if($threads) {
        // Will be decremented with thread deletion anyway
        $sql .= "\nJOIN threads t ON t.id = p.thread_id
                WHERE p.user_id = '$id' AND t.user_id != '$id' AND p.deleted = 8";
    } else {
        $sql .= "\nWHERE p.user_id = '$id' AND p.deleted = 8";
    }

    $sql .=     "\n) p ON p.user_id = u.user_id
            SET u.posts = u.posts $op p.psts";
    if($conn->query($sql) === FALSE) {
        return ["", "SC8"];
    }

    $err = checkEmptyThreads();
    if($err[0] !== "pass") {
        return $err;
    }

    if(!$threads) {
        return ["pass"];
    }

    $sql = "SELECT id FROM threads WHERE user_id = '$id'";

    $result = $conn->query($sql);
    if($result->num_rows === 0) {
        return ["pass"];
    }

    // Update counts for each deleted or restored thread
    while($row = $result->fetch_assoc()) {
        $err = countForThread($row["id"], $rest);
        if($err[0] !== "pass") {
            return $err;
        }
    }

    return ["pass"];
}

function checkEmptyThreads() : array {
    $path = $_SERVER['DOCUMENT_ROOT'];
    require_once $path . '/functions/.connect.php' ;
    $conn = getConn();

    $sql = "UPDATE categories c, threads t, users u
            SET c.threads = c.threads - 1, u.threads = u.threads - 1, t.deleted = t.deleted | 2
            WHERE t.posts = 0 AND t.category_id = c.id AND t.user_id = u.user_id AND t.deleted = 0";

    if($conn->query($sql) === FALSE) {
        return ["", "SC9"];
    }

    $sql = "UPDATE categories c, threads t, users u
            SET c.threads = c.threads + 1, u.threads = u.threads + 1, t.deleted = t.deleted & ~2
            WHERE t.posts != 0 AND t.category_id = c.id AND t.user_id = u.user_id AND t.deleted = 2";

    if($conn->query($sql) === FALSE) {
        return ["", "SC10"];
    }

    return ["pass"];
}