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

    if(include($path . '/functions/validateSession.php')) {
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT clearance FROM users WHERE user_id = '$user_id'";
        $result = $conn->query($sql);
        $myClearance = $result->fetch_assoc()["clearance"];
    } else {
        $myClearance = 0;
    }

    $sql = "SELECT 
                t.name,
                t.id, 
                t.slug,
                t.created, 
                t.posts,
                cr.clearance,
                cr.username,
                cr.handle,
                u.username AS lastUser,
                u.handle AS lastHandle,
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
                SELECT username, handle, user_id FROM users
            ) u ON u.user_id = lp.user_id
            INNER JOIN users cr 
                ON cr.user_id = t.user_id
            WHERE 
                c.slug = '$slug' AND t.deleted = 0
            ORDER BY 
                lp.created DESC
            LIMIT 20 OFFSET $page";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        $data = [];
        while($thread = $result->fetch_assoc()) { 
            if($thread["clearance"] < $myClearance && $myClearance > 1) {
                $thread["clearance"] = 1;
            } else {
                $thread["clearance"] = 0;
            }
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
        <div class="thread-wrapper">
            <span class="main-wrapper">
                <span class="thread-name">
                    <a href="/thread/<?= $thread['slug'] ?>"><?= $thread['name'] ?></a>
                </span>
                <span class="thread-creator">
                    <a href="/user/<?= $thread['handle'] ?>"><?= $thread['username'] ?></a>
                </span>
                <span class="created"><?= $thread['created'] ?></span>
            </span>
            <span class="details-wrapper">
                <span class="last-wrapper">
                    <span class="last-post"><?= $thread['lastPost'] ?></span>
                    <span class="last-user">
                        <a href="/user/<?= $thread['lastHandle'] ?>"><?= $thread['lastUser'] ?></a>
                    </span>
                </span>
                <span class="count"><?= $thread['posts'] ?></span>
            </span>
            <?php if($thread['clearance'] === 1) {?>
            <button class="delete-button" onclick="createConfirmation('delete <?= $thread['username'] ?>\'s post', '', deletePost, '<?= $thread['id'] ?>')">delete</button>
            <?php } ?>
        </div>

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

    if ($result->num_rows === 1) {
        return $result->fetch_assoc()["threads"];
    } else {
        return 0;
    }
}

function getPathName(string $slug) {
    $path = $_SERVER['DOCUMENT_ROOT'];
    include $path . '/functions/.connect.php' ;

    // Get connection
    $conn = getConn();

    // Check connection
    if ($conn->connect_error) {
        return [];
    }

    // Category
    $sql = "SELECT c.name
    FROM categories c
    WHERE c.slug = '$slug'";

    $result = $conn->query($sql);
    if ($result->num_rows === 1) {
        return [["topic/" . $slug, $result->fetch_assoc()["name"]]];
    }

    return [];
}