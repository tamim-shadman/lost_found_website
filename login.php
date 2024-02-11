<?php
session_start();

// Check for a valid cookie.
if (isset($_COOKIE['session_id'])) {
    // You can remove this line as it's not necessary in this context.
    // session_id($_COOKIE['session_id']);
}

$conn = new mysqli("localhost", "root", "", "l&fbd");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST["Email"];
$pwd = $_POST["Password"];

$sql = "SELECT Email, Password FROM signup";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($email == $row['Email'] && $pwd == $row['password']) {
            $_SESSION['user_email'] = $row['Email']; // Store the user's email in the session variable.

            // Set a cookie.
            setcookie('session_id', session_id(), time() + 60 * 60 * 24 * 30, "/"); // 30 days, '/' for the whole domain
            
            // Redirect to the index page
            header("Location: index.php");
            exit();
        }
    }

    mysqli_free_result($result);
    
    // If the login is not successful, you can provide an error message or redirect to a login error page.
    echo "Login failed. Invalid email or password.";
}

$conn->close();
?>
