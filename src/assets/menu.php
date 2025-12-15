<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/functions/.connect.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/functions/lang.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/functions/require/notifications.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/assets/generateSVG.php';

function generateMenu() {
    if(!session_id()) {
        session_start();
    }

    if(isset($_SESSION['user_id'])) {
        $conn = getConn();

        $user_id = $_SESSION['user_id'];
        $sql = "SELECT darkmode, handle FROM users WHERE user_id = '$user_id'";
        $result = $conn->query($sql);

        $info = $result->fetch_assoc();

        $res = NewNotifCount($user_id);
        
        $notifs = 0;
        if($res[0] === "pass") {
            $notifs = (int)$res[1];
        }
        
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        $notifClass = " none";
        if($notifs > 0 && $path !== "/profile/notifications") {
            $notifClass = "";
        }
    }
    
    ?>
    <link rel="stylesheet" href="/styles/menu.css">
    <script src="/scripts/main.js"></script>
    <script src="/scripts/account.js"></script>
    <div id="progress-bar"></div>
    <div class="menu">
        <a class="menu-button icon menu-left" href="/"><?= generateHome() ?></a>
        <?php
        if(isset($_SESSION['user_id'])) {
            ?>
            <span class="menu-button split-right menu-right profile-menu" onclick="toggleMenuOptions()">
                <span class="menu-user-info">
                    <?= $info["handle"]; ?>
                    <a href="/profile/notifications" class="new-notifications-indicator <?= $notifClass; ?>"><?= $notifs; ?></a>
                </span>
                <span id="profile-options-wrapper">
                    <span id="profile-options">
                        <a class="menu-button" href="/profile/settings">settings</a>
                        <a class="menu-button" href="/profile/moderation">moderation</a>
                        <a class="menu-button" href="/profile/notifications">notifications</a>
                    </span>
                </span>
            </span>
            <span class="menu-button icon menu-right" onclick="logout()"><?= generateLogout() ?></span>
            <script> 
                setAppearance(<?= $info["darkmode"]; ?>);
            </script>
            <?php
        } else {
            ?>
            <span class="menu-button split-right menu-right" onclick="createLogin()"><?= getLang("login") ?></span>
            <span class="menu-button menu-right" onclick="createSignUp()"><?= getLang("signUp") ?></span>
            <?php 
        } 
        ?>
    </div>
    <div id="menu-buffer"></div>
<?php }

function getCategory(string $slug) {
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
}

function generateMenuPath(int $type = 0, string $slug = "") : string {
    // 0 home, 1 topic, 2 thread

    $out = '<a href="/">Home</a>';

    if($type === 0) {
        return $out;
    }

    if($slug === "") {
        return "";
    }

    if($type === 1) {
        $name = getTopicPathName($slug);
        if($name === "") {
            return "";
        }
        $out .= ' > <a onclick="getThreads()">' . $name . '</a>';
        return $out;
    } else if($type === 2) {
        $pathArr = getThreadPathName($slug);
        if(count($pathArr) !== 3) {
            return "";
        }
        $out .= ' > <a href="' . $pathArr[0] . '">' . $pathArr[1] . '</a>';
        $out .= ' > <a onclick="getPosts()">' . $pathArr[2] . '</a>';
        return $out;
    }

    return "";
}

function getTopicPathName(string $slug) : string {
    // Get connection
    $conn = getConn();

    // Category
    $sql = "SELECT c.name
    FROM categories c
    WHERE c.slug = '$slug'";

    $result = $conn->query($sql);
    if ($result->num_rows === 1) {
        return $result->fetch_assoc()["name"];
    }

    return "";
}

function getThreadPathName(string $slug) : array {
    // Get connection
    $conn = getConn();

    // Category
    $sql = "SELECT c.slug AS c_slug, c.name AS c_name, t.name AS t_name
    FROM categories c
    JOIN threads t ON t.category_id = c.id
    WHERE t.slug = '$slug' AND t.deleted = 0";

    $result = $conn->query($sql);
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        return ["/topic/" . $row["c_slug"], $row["c_name"], $row["t_name"]];
    } 
    return [];
}

