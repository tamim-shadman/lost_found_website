<?php
	session_start();

	$conn = new mysqli("localhost", "root", "", "l&fbd");

	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	$fname = $_POST["Fname"];
	$lname = $_POST["lname"];
	$phn = $_POST["Mobile"];
	$email = $_POST["Email"];
	$pwd = $_POST["password"];

	$sql = "INSERT INTO signup(Fname, lname, Mobile, Email, password ) VALUES
	('$fname','$lname', '$phn', '$email', '$pwd')";
	$query = mysqli_query($conn, $sql);

	if ($query) {
		// Create a session for the user
		$_SESSION['user_email'] = $email;

		// Set a cookie for the user (optional)
		// setcookie("user_email", $email, time() + (86400 * 30), "/"); // 86400 seconds = 1 day

		header("Location: index.php");
		exit();
	} else {
		// Redirect to signup page if registration fails
		header("Location: admin_signed_up2.php");
		exit();
	}
?>
