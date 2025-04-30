<?php $path = $_SERVER['DOCUMENT_ROOT']; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forums</title>
    <link rel="stylesheet" href="/styles/main.css" />
    <link rel="stylesheet" href="/styles/topics.css" />
</head>
<body>
    <?php include $path . "basic/menu.php"; ?>

    <?php
    $configs = include($path . 'functions/.config.php');
    extract($configs);

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT name, description, created, threads, posts 
            FROM categories";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<a href=\"/topic?c=" . $row["name"] . "\"><div class=\"category\"><span class=\"name\">" . $row["name"] . "</span><span class=\"description\">" . $row["description"] . "</span><span class=\"date\">" . $row["created"] . "</span><span class=\"thread-count\">" . $row["threads"]. "</span><span class=\"post-count\">" . $row["posts"]. "</span></div></a>";
      }
    } else {
      echo "ERROR: Failed to load";
    }

    $conn->close();
    ?>

    

    <?php include $path . "basic/footer.php"; ?>
</body>
</html>