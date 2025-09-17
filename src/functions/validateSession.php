<?php

if(!function_exists("validateSession")) {

function validateSession() {
    $path = $_SERVER['DOCUMENT_ROOT'];
    include $path . '/functions/.connect.php' ;
    include $path . '/functions/sessionUpdates.php';

    // Get connection
    $conn = getConn();

    if(!session_id()) {
        session_start();
    }

    if(isset($_SESSION['user_id'], $_SESSION['session_id'])) {
        $session_id = $_SESSION['session_id'];

        $sql = "SELECT ip, user_agent, user_id, datetime FROM sessions WHERE session_id='$session_id' LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $db_ip = $row["ip"];
            $db_user_agent = $row["user_agent"];
            $db_user_id = $row["user_id"];
            $db_datetime = $row["datetime"];

            if($_SESSION['user_id'] === $db_user_id && $_SERVER['REMOTE_ADDR'] === $db_ip && $_SERVER['HTTP_USER_AGENT'] === $db_user_agent) {
                $diff = time() - strtotime($db_datetime);
                // Sessions is valid for max. 5 days
                if($diff <= 60 * 60 * 24 * 5) {
                    $r = rand(0, 19); // 1 in 20 chance to update session time
                    if($r === 0) {
                        $dtime = date('Y-m-d H:i:s');
                        $sql = "UPDATE sessions SET datetime='$dtime' WHERE session_id = '$session_id'";
                        if ($conn->query($sql) === TRUE) {
                            return true;
                        }
                    } else {
                        return true;
                    }
                } else {
                    $r = rand(0, 99); // 1 in 100 chance to clear expired sessions
                    if($r === 0) {
                        deleteExpiredSessions(); // Any errors are ignored
                    }
                }
            }
        }
    }

    if(isset($_SESSION["user_id"])) {
        unset($_SESSION['user_id']);
    }

    if(isset($_SESSION["session_id"])) {
        unset($_SESSION['session_id']);
    }

    return false;
}

}