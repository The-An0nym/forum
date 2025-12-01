<?php 
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;
require_once $path . "/assets/menu.php";

// Get connection
$conn = getConn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quir</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/styles/main.css" />
    <link rel="stylesheet" href="/styles/topics.css" />
</head>
<body>
    <?php generateMenu() ?>
    
    <div id="menu-path">
        <?= generateMenuPath(0) ?>
    </div>

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

    <?php require_once $path . "/assets/footer.php"; ?>
</body>
</html>