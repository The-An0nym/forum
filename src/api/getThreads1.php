<?php
function getDBConnection() : mysqli {
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


function generateThreads(string $slug, int $page) {
    $conn = getDBConnection();

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
                lp.created DESC
            LIMIT 20 OFFSET $page";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) { ?>
            <a class="thread-wrapper" href="/thread/<?= $thread['slug'] ?>">
                    <div class="thread">
                        <span class="main-wrapper">
                            <span class="thread-name"><?= $thread['name'] ?></span>
                            <span class="created"><?= $thread['created'] ?></span>
                        </span>
                        <span class="details-wrapper">
                            <span class="last-wrapper">
                                <span class="last-post"><?= $thread['lastPost'] ?></span>
                                <span class="last-user"><?= $thread['lastUser'] ?></span>
                            </span>
                            <span class="count"><?= $thread['posts'] ?></span>
                        </span>
                    </div>
                </a>
        <?php }
    } else {
        return [];
    }

    $conn->close();
}

function getThreadCount(string $slug) {
    if($slug = "") {
        return 0;
    }

    $conn = getDBConnection();

    
    $sql = "SELECT COUNT(*) AS total_threads
            FROM threads t
            JOIN categories c ON c.id = t.category_id
            WHERE c.slug = '$slug'";
    $result = $conn->query($sql);

    try {
        $total_threads = $result->fetch_assoc()["total_threads"];
    } catch {
        $total_threads = 0;
    }

    echo $total_threads;
}