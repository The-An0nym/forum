<?php
$path = $_SERVER['DOCUMENT_ROOT'];

    $configs = include($path . '/functions/.config.php');
    extract($configs);

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }

    if(isset($_GET['t'])) {
        $thread_name = $_GET['t'];
        $sql = "SELECT 
                    users.username, 
                    users.posts,
                    posts.post_id, 
                    posts.user_id, 
                    posts.content, 
                    posts.created, 
                    posts.edited,
                FROM 
                    posts 
                LEFT JOIN 
                    users ON users.user_id = posts.user_id 
                WHERE 
                    posts.thread = '$thread_name'
                ORDER BY 
                    posts.created ASC";
        $result = $conn->query($sql);

        session_start();

        $data = [];
        if ($result->num_rows > 0) {
            $indx = 0;
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $post = new stdClass();
                $post->username = $row["username"];
                $post->userPostCount = $row["posts"];
                $post->id = $row["post_id"];
                $post->content = $row["content"];
                $post->created = $row["created"];
                $post->edited = $row["edited"];
                if(isset($_SESSION["user_id"])) {
                if($row["user_id"] == $_SESSION["user_id"]) {
                    $post->editable = true;
                } else {
                    $post->editable = false;
                }} else {
                    $post->editable = false;
                }
                $data[] = $post;
                $indx += 1;
            }

        $dataJSON = json_encode($data);
        echo $dataJSON;

        } else {
        echo "ERROR: Failed to load";
        }
    } else {
        header('Location: /');
        die();
    }

    $conn->close();
    ?>   