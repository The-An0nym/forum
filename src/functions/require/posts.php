<?php
include $_SERVER['DOCUMENT_ROOT'] . '/functions/.connect.php';

function getPosts(string $slug, int $page) :array {   
    include $_SERVER['DOCUMENT_ROOT'] . '/functions/validateSession.php';;
    include $_SERVER['DOCUMENT_ROOT'] . '/functions/time.php';

    // Get connection
    $conn = getConn();

    // Check connection
    if ($conn->connect_error) {
        return [];
    }   

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
                u.username, 
                u.handle,
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
            AND
                t.deleted = 0
            ORDER BY 
                p.created ASC
            LIMIT 20 OFFSET $offset";
    
    $result = $conn->query($sql);

    $data = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if($row["clearance"] < $myClearance) {
                $row["clearance"] = 1;
            } else {
                $row["clearance"] = 0;
            }

            $row["created"] = timeAgo($row["created"]);

            $data[] = $row;
        }
        return $data;
    } else {
        return [];
    }
}

function generateHTMLFromPosts(string $slug, int $page) {
    $posts = getPosts($slug, $page);

    foreach($posts as $post) {?>
        <div class="post" id="<?= $post['post_id'] ?>">
            <span class="user-details">
                <img class="profile-picture" src="/images/profiles/<?= $post['image_dir'] ?>">
                <span class="username"><a href="/user/<?= $post['handle'] ?>"><?= $post['username'] ?></a></span>
                <span class="user-post-count"><?= $post['posts'] ?></span>
            </span>
            <span class="post-data">
                <span class="content"><?= $post['content'] ?></span>
                <span class="post-metadata">
                    <?php 
                    echo '<span class="created">' . $post['created'] . '</span>';
                    if($post['edited'] === "1") {
                        echo '<span class="edited">edited</span>';
                    }
                    if(isset($_SESSION["user_id"])) {
                        if($post["user_id"] === $_SESSION["user_id"]) {
                            echo '<button class="edit-button" onclick="editPost(\'' . $post['post_id'] . '\')">edit</button>';
                            echo '<button class="delete-button" onclick="createConfirmation(\'delete ' . $post['username'] . '\\\'s post\', \'\', deletePost, \'' . $post['post_id'] . '\')">delete</button>';
                        } else if($post['clearance'] === 1) {
                            echo '<button class="delete-button" onclick="createModeration(\'deleting ' . $post['username'] . '\\\'s post\', deletePost, \'' . $post['post_id'] . '\')">delete</button>';
                        } else {
                            echo '<button class="report-button" onclick="createReport(0, \'' . $post['post_id'] . '\')">Report</button>';
                        }
                    } ?>
                </span>
            </span>
        </div>
    <?php 
    }
}

function getPostCount(string $slug) : int {
    if($slug === "") {
        return 0;
    }

    // Get connection
    $conn = getConn();

    $sql = "SELECT posts FROM threads WHERE slug = '$slug'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        return (int)$result->fetch_assoc()["posts"];
    } else {
        return 0;
    }
}

function getPostsJson(string $slug, int $page = 1) : string {
    if(!session_id()) {
        session_start();
    }

    $post_count = getPostCount($slug);

    if($post_count === 0) { 
        return jsonErr("emptyThrd");
    }

    if($page === -1) {
        $page = ceil($post_count / 20);
    } else {
        $page = min($page, ceil($post_count / 20));
    }


    $posts = getPosts($slug, $page);
    $data = [];

    // output data of each post
    foreach($posts as $post) {
        $p = new stdClass();
        $p->username = $post["username"];
        $p->handle = $post["handle"];
        $p->imageSrc = $post["image_dir"];
        $p->userPostCount = $post["posts"];
        $p->id = $post["post_id"];
        $p->content = $post["content"];
        $p->created = $post["created"];
        $p->edited = $post["edited"];
        if(isset($_SESSION["user_id"])) {
            if($post["user_id"] == $_SESSION["user_id"]) {
                $p->editable = true;
            } else {
                $p->editable = false;
            }
        } else {
            $p->editable = false;
        }
        $p->deletable = $post["clearance"];
        $data[] = $p;
    }

    $dataJSON = json_encode(
        array(
            "status" => "pass",
            "data" => array(
                "posts" => $data,
                "amount" => $post_count
            )
        )
    );

    return $dataJSON;
}