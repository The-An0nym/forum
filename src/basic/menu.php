<?php

function generateMenu($paths) {
    $HTMLpath = '<a href="/">home</a>';
    foreach($paths as $path) {
        $HTMLpath .= ' > ';
        $HTMLpath .= '<a href=/' . $path[0];
        $HTMLpath .= '>' . $path[1] . '</a>';
    }

    if(!session_id()) {
        session_start();
    }

    if(isset($_SESSION['user_id'])) {
        include $_SERVER['DOCUMENT_ROOT'] . '/functions/.connect.php';
        $conn = getConn();

        $user_id = $_SESSION['user_id'];
        $sql = "SELECT darkmode, handle FROM users WHERE user_id = '$user_id'";
        $result = $conn->query($sql);

        $info = $result->fetch_assoc();
    }
    
    ?>
    <link rel="stylesheet" href="/styles/menu.css">
    <script src="/scripts/main.js"></script>
    <div class="menu">
        <?php
        if(isset($_SESSION['user_id'])) {
            ?>
            <span class="profile menu-button"><a href="/profile/"><?= $info["handle"]; ?></a></span>
            <span class="logout menu-button"><a href="/logout/">logout</a></span>
            <span class="menu-path"><?= $HTMLpath ?></span>
            <span class="mode menu-button" onclick="toggle()">Toggle Mode</span>
            <script> 
                toggle(<?= $info["darkmode"]; ?>);
            </script>
            <?php
        } else {
            ?>
            <script src="/scripts/account.js"></script>
            <span class="menu-path"><?= $HTMLpath ?></span>
            <span class="login menu-button" onclick="createLogin()">login</span>
            <span class="sign-up menu-button" onclick="createSignUp()">sign up</span>
            <?php 
        } 
        ?>
    </div>
    <div id="menu-buffer"></div>
<?php }

function getCategory(string $slug) {
    $path = $_SERVER['DOCUMENT_ROOT'];
    include $path . '/functions/.connect.php';

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

function generateProfileMenu() {
    ?>
    <div class="profile-menu">
        <a class="menu-button" href="/profile/settings">Settings</a>
        <a class="menu-button" href="/profile/moderation">Moderation</a>
        <a class="menu-button" href="/profile/notifications">Notifications</a>
    </div>
    <?php
}