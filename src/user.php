<?php 
$path = $_SERVER['DOCUMENT_ROOT']; 

if(!session_id()) {
    session_start();
} 

include $path . "/basic/menu.php";

if(isset($_GET["s"])) {
    $handle = $_GET["s"];
} else {
    $handle = "";
}

$sql = "SELECT username, user_id, image_dir, posts FROM users WHERE handle = '$handle' LIMIT 1";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$username = $row["username"];
$user_id= $row["user_id"];
$image_dir = $row["image_dir"];
$posts = $row["posts"];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quir | Threads</title>
    <link rel="stylesheet" href="/styles/main.css" />
    <link rel="stylesheet" href="/styles/threads.css" />
</head>
<body>
    <?php generateMenu([]) ?>

    <div id="global">

        <div id="user-info">
            <img class="user-image" src="/images/profiles/<?= $image_dir; ?>">
            <span class="username"><?= $username ?></span>
            <span class="handle"><?= $handle ?></span>
        </div>

        To come...

    </div>

    <script src="/scripts/main.js"></script>
    <script> 
        const slug = "<?= $slug; ?>";
    </script>
    <script src="/scripts/user.js"></script>

    <?php include $path . "/basic/footer.php"; ?>
</body>
</html>