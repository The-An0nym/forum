<?php $path = $_SERVER['DOCUMENT_ROOT']; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forums</title>
    <link rel="stylesheet" href="/styles/main.css" />
    <link rel="stylesheet" href="/styles/threads.css" />
</head>
<body>
    <?php include $path . "/basic/menu.php"; ?>

    <?php
    $configs = include($path . '/functions/.config.php');
    extract($configs);

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }

    $category = str_replace("-", " ", $_GET['n']);
    $sql = "SELECT 
                t.name, 
                t.created, 
                t.posts,
                u.username AS lastUser,
                lp.created AS lastCreated
            FROM 
                threads t
            LEFT JOIN (
                SELECT 
                    p1.thread, 
                    p1.user_id,
                    p1.created
                FROM 
                    posts p1
                INNER JOIN (
                    SELECT 
                        thread, 
                        MAX(created) AS maxCreated
                    FROM 
                        posts
                    GROUP BY 
                        thread
                ) p2 ON p1.thread = p2.thread AND p1.created = p2.maxCreated
            ) lp ON t.name = lp.thread
            INNER JOIN (
                    SELECT username, user_id FROM users
                ) u ON u.user_id = lp.user_id
                WHERE t.category = \"$category\"";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $posts = 0;
        echo "<a href=\"/thread/" . str_replace(" ", "-", $row["name"]) . "\"><div class=\"thread\"><span class=\"name\">" . $row["name"] . "</span><span class=\"created\">" . $row["created"] . "</span><span class=\"lastCreated\">" . $row["lastCreated"]. "</span><span class=\"lastUser\">" . $row["lastUser"]. "</span><span class=\"post-count\">" . $row["posts"]. "</span></div></a>";
      }
    } else {
      echo "ERROR: Failed to load";
    }

    $conn->close();
    ?>

    

    <?php include $path . "/basic/footer.php"; ?>
</body>
</html>