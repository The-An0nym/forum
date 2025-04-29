<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forums</title>
    <link rel="stylesheet" href="main.css" />
</head>
<body>
    <?php include "basic/header.php"; ?>

    <?php
    $configs = include('functions/.config.php');
    extract($configs);

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT c.name, c.created, COALESCE(t.numThreads, 0) AS numThreads, COALESCE(p.numPosts, 0) AS numPosts 
            FROM categories c 
            LEFT JOIN ( SELECT category, COUNT(*) AS numThreads FROM threads GROUP BY category ) t 
                ON t.category = c.name 
            LEFT JOIN ( SELECT t.category, COUNT(*) AS numPosts FROM posts p 
                INNER JOIN threads t 
                    ON p.thread = t.name GROUP BY t.category ) p 
                ON p.category = c.name
            ";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<a href=\"/topic.php?c=" . $row["name"] . "\"><div class=\"category\"><span class=\"name\">" . $row["name"] . "</span><span class=\"date\">" . $row["created"] . "</span><span class=\"thread-count\">" . $row["numThreads"]. "</span><span class=\"post-count\">" . $row["numPosts"]. "</span></div></a>";
      }
    } else {
      echo "ERROR: Failed to load";
    }

    $conn->close();
    ?>

    

    <?php include "basic/footer.php"; ?>
</body>
</html>