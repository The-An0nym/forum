<?php
include $_SERVER['DOCUMENT_ROOT'] . '/functions/.connect.php';

function getThreads(string $slug, int $page) {
    include $_SERVER['DOCUMENT_ROOT'] . '/functions/validateSession.php';;

    // Get connection
    $conn = getConn();

    if(validateSession()) {
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT clearance FROM users WHERE user_id = '$user_id'";
        $result = $conn->query($sql);
        $myClearance = $result->fetch_assoc()["clearance"];
    } else {
        $myClearance = 0;
    }

    $offset = (max($page, 1) - 1) * 20;

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
                    WHERE 
                        deleted = 0
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
            LIMIT 20 OFFSET $offset";

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
    $threads = getThreads($slug, $page);

    include $_SERVER['DOCUMENT_ROOT'] . '/functions/time.php';

    foreach($threads as $thread) {?>
        <div class="thread-wrapper">
            <span class="main-wrapper">
                <span class="thread-name">
                    <a href="/thread/<?= $thread['slug'] ?>"><?= $thread['name'] ?></a>
                </span>
                <span class="thread-creator">
                    <a href="/user/<?= $thread['handle'] ?>"><?= $thread['username'] ?></a>
                </span>
                <span class="created"><?= dateTimeStamp($thread['created']) ?></span>
            </span>
            <span class="last-wrapper">
                <span class="last-post"><?= $thread['lastPost'] ?></span>
                <span class="last-user">
                    <a href="/user/<?= $thread['lastHandle'] ?>"><?= $thread['lastUser'] ?></a>
                </span>
            </span>
            <span class="count"><?= $thread['posts'] ?></span>
            <?php if($thread['clearance'] === 1) {?>
            <button class="delete-button" onclick="createModeration('deleting <?= $thread['username'] ?>\'s post', deleteThread, '<?= $thread['id'] ?>')">delete</button>
            <?php } ?>
        </div>

        <?php 
    }
}

function getThreadCount(string $slug) : int {
    if($slug === "") {
        return 0;
    }

    // Get connection
    $conn = getConn();

    $sql = "SELECT threads FROM categories WHERE slug = '$slug'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        return (int)$result->fetch_assoc()["threads"];
    } else {
        return 0;
    }
}

function getThreadsJson(string $slug, int $page = 1) : string {
    if(!session_id()) {
        session_start();
    }

    $thread_count = (int)getThreadCount($slug);

    if($thread_count === 0) {
        return jsonErr("emptyCat");
    }

    if($page === -1) {
        $page = ceil($thread_count / 20);
    } else {
        $page = min($page, ceil($thread_count / 20));
    }

    $threads = getThreads($slug, $page);

    $data = [];

    // output data of each thread
    foreach($threads as $thread) {
        $t = new stdClass();
        $t->name = $thread["name"];
        $t->slug = $thread["slug"];
        $t->id = $thread["id"];
        $t->created = $thread["created"];
        $t->postCount = $thread["posts"];
        $t->lastUser = $thread["lastUser"];
        $t->lastHandle = $thread["lastHandle"];
        $t->lastPost = $thread["lastPost"];
        $t->creator = $thread["username"];
        $t->creatorHandle = $thread["handle"];
        $t->deletable = $thread["clearance"];
        $data[] = $t;
    }

    $dataJSON = json_encode(
        array(
            "status" => "pass",
            "data" => array(
                "threads" => $data,
                "amount" => $thread_count
            )
        )
    );
    return $dataJSON;
}