<?php 
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . "/basic/menu.php";

// Get connection
$conn = getConn();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quir</title>
    <link rel="stylesheet" href="/styles/main.css" />
    <link rel="stylesheet" href="/styles/topics.css" />
</head>
<body>
    <?php generateMenu([]) ?>

    <div class="container">
      <?php
      $sql = "SELECT name, slug, description, created, threads, posts 
              FROM categories";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
      // output data of each row
      while($row = $result->fetch_assoc()) {
          echo "
            <a href=\"/topic/" . $row["slug"] . "\">
              <div class=\"category\">
                <span>
                  <span class=\"name\">" . $row["name"] . "</span>
                  <span class=\"description\">" . $row["description"] . "</span>
                </span>
                <span class=\"count-wrapper\">
                  <span class=\"count\">" . $row["threads"]. "</span>
                  <span class=\"count\">" . $row["posts"]. "</span>
                </span>
              </div>
            </a>";
        }
      } else {
        echo "ERROR: Failed to load";
      }

      $conn->close();
      ?>
    </div>

    <script src="/scripts/main.js"></script>

    <?php include $path . "/basic/footer.php"; ?>
</body>
</html>