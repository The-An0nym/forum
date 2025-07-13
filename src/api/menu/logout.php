<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/errors.php' ;
include $path . '/functions/sessionUpdates.php' ;

echo response();

function response() {
    // Get connection
    $conn = getConn();

    if(!session_id()) {
        session_start();
    }

    $err = jsonEncodeErrors(clearCurrSession());
    if($err !== "") {
        return $err;
    }
    
    return pass();
}