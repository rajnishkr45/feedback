<?php
function getActiveSession($conn) {
    $result = $conn->query("SELECT session_id, session_name FROM sessions WHERE status='Active' LIMIT 1");
    if ($result->num_rows > 0) {
        return $result->fetch_assoc(); // returns array with session_id & session_name
    }
    return null; // No active session found
}
?>
