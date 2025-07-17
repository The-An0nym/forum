<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/validateSession.php';
include $path . '/functions/require/notifications.php' ;
include $path . '/functions/errors.php' ;

echo response();

function response() : string {
    if(!validateSession()) {
        return jsonErr("login");
    }

    $page = 0;
    if(isset($_POST["p"])) {
        $page = (int)$_POST["p"];
    }

    $html = generateNotifsHTML($page);

    return json_encode(
        array(
            "status" => "pass",
            "data" => $html;
        )
    )
}