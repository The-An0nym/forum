<?php
// CONNECT
$configs = include('functions/.config.php');
extract($configs);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

session_start();
if(!include('functions/validateSession.php')) {
    echo "Please Login to comment";
} else {
    $session_user_id = $_SESSION['user_id'];
    $sql = "SELECT username FROM users WHERE user_id='$session_user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc()["username"];
        echo "Commenting as $user";
    } else {
        echo "An error has occured. Please login again";
    }
}

// STORE DATA
if (isset($_POST["submit"])) {
   $_SESSION['cont'] = $_POST['input'];
   header("Location: comments.php");
   exit;
}

// ADD COMMENT TO DATABASE
if (isset($_SESSION["cont"])) {
    if(!empty($_SESSION["cont"])) {
        if(empty($user)) {
            echo "Login to comment";
        } else {
            // idk about mysql_real_escape_string ??
            $cont = htmlspecialchars($_SESSION["cont"]);
            $dtime = date('Y-m-d H:i:s');
            $sql = "INSERT INTO comments (user_id, content, datetime)
            VALUES ('$user', '$cont', '$dtime')";
            if ($conn->query($sql) === TRUE) {
              popUp("Comment posted successfully");
            } else {
              popUp("Error: " . $sql . "<br>" . $conn->error);
            }
        }
    } else {
        popUp("ERROR: No input");
    }
unset($_SESSION["cont"]);
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Comment Section</title>
    <link rel="stylesheet" href="styles/main.css" />
  </head>
  <body>
    <?php include('basic/header.php');?>
    <section class="global">
      <form action="" method="POST">
        <label for="comment-input">Leave a comment:</label><br />
        <textarea id="comment-input" name="input"></textarea><br />
        <button type="submit" name="submit">Submit</button>
      </form>
      <div class="comments">
        <?php
        // Recall all entires until now

        $sql = "SELECT user_id, content, datetime FROM comments ORDER BY datetime DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          // output data of each row
          while($row = $result->fetch_assoc()) {
            echo "<div class=\"comment\"><div class=\"comment-header\"><span class=\"author\">" . $row["user_id"]. "</span><span class=\"date\">" . $row["datetime"] . "</span></div><span class=\"comment-content\">" . $row["content"]. "</span></div>";
          }
        } else {
          echo "0 Results";
        }

        ?>
      </div>
    </section>

    <?php
        function popUp($message) {
            echo "<div id=\"pop-up\">$message</div>";
            echo "<script>
                    function hidePopUp() {
                        const popUp = document.getElementById('pop-up');
                        popUp.style.display = 'none';
                    }
                    setTimeout(hidePopUp, 5000);
                </script>";
        }
    ?>
  </body>
</html>

<?php
$conn->close();
?>