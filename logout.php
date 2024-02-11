<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Remove the session cookie by setting its expiration to the past
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Unset any custom cookies
setcookie('session_id', '', time() - 3600, '/'); // Assuming you set a custom session ID cookie

// Redirect to the login page or any other desired page
header("Location: index.php");
exit();
?>
