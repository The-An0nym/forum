<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/require/threads.php';

$totalThreads = 0;

if(isset($_GET['s'], $_GET['p'])) {
    $slug = $_GET['s'];
    $page = $_GET['p'] * 20;
    
    $threads = getThreads($slug, $page);

    if($threads > 1) {
        $totalThreads = array_shift($threads);

        // output data of each thread
        foreach($threads as $thread) { ?>
            <a class="thread-wrapper" href="/thread/<?= $thread['slug'] ?>">
                <div class="thread">
                    <span class="main-wrapper">
                        <span class="thread-name"><?= $thread['name'] ?></span>
                        <span class="created"><?= $thread['created'] ?></span>
                    </span>
                    <span class="details-wrapper">
                        <span class="last-wrapper">
                            <span class="last-post"><?= $thread['lastPost'] ?></span>
                            <span class="last-user"><?= $thread['lastUser'] ?></span>
                        </span>
                        <span class="count"><?= $thread['posts'] ?></span>
                    </span>
                </div>
            </a>
        <?php 
        }
    } else {
        echo "An error has occured";
    }
} else {
    echo "An error has occured";
}