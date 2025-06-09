<?php 
$path = $_SERVER['DOCUMENT_ROOT']; 

if(!session_id()) {
    session_start();
} 

include $path . "/basic/menu.php";

if(isset($_GET["s"])) {
    $slug = $_GET["s"];
} else {
    $slug = "";
}

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
    <?php generateMenu(getPathName($slug)) ?>

    <div id="global">

        <div id="user-info">
            to come...
        </div>
        
    </div>

    <script src="/scripts/main.js"></script>
    <script> 
        const slug = "<?= $slug; ?>";
    </script>
    <script src="/scripts/user.js"></script>

    <?php include $path . "/basic/footer.php"; ?>
</body>
</html>