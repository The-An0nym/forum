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

function getThreads(string $slug, int $page) {
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
        $data = [];
        while($thread = $result->fetch_assoc()) { 
            $data[] = $thread;
        }
        return $data;
    } else {
        return [];
    }
}

function generateHTMLFromThreads(string $slug, int $page) {
    $threads = getThreads($slug, $page * 20);
    foreach($threads as $thread) {?>
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
        <?php 
    }
}

function getThreadCount(string $slug) {
    if($slug === "") {
        return 0;
    }

    $conn = getDBConnection();

    $sql = "SELECT threads FROM categories WHERE slug = '$slug'";
    $result = $conn->query($sql);

    if ($result->num_rows !== 0) {
        return $result->fetch_assoc()["threads"];
    } else {
        return 0;
    }
}