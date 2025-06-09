<?php 
$path = $_SERVER['DOCUMENT_ROOT']; 
include $path . '/functions/.connect.php' ;
include $path . "/basic/menu.php";

if(!session_id()) {
    session_start();
} 

// Get connection
$conn = getConn();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if(isset($_GET["s"])) {
    $handle = $_GET["s"];
} else {
    $handle = "";
}

$load = true;

$sql = "SELECT username, user_id, image_dir, posts FROM users WHERE handle = '$handle' LIMIT 1";
$result = $conn->query($sql);
if($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $username = $row["username"];
    $user_id= $row["user_id"];
    $image_dir = $row["image_dir"];
    $posts = $row["posts"];
} else {
    $load = false;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quir | Threads</title>
    <link rel="stylesheet" href="/styles/main.css" />
    <link rel="stylesheet" href="/styles/user.css" />
</head>
<body>
    <?php generateMenu([]) ?>

    <?php if($load) {?>

    <div id="global">

        <div id="user-info">
            <img class="user-image" src="/images/profiles/<?= $image_dir; ?>">
            <span class="username"><?= $username ?></span>
            <span class="handle">@<?= $handle ?></span>
            <span class="posts"><?= $posts ?></span>
        </div>

        To come...

    </div>

    <script> 
        const slug = "<?= $slug; ?>";
    </script>
    <script src="/scripts/user.js"></script>
    <?php } ?>
    <script src="/scripts/main.js"></script>

    <?php include $path . "/basic/footer.php"; ?>
</body>
</html>