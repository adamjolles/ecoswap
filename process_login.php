<?php
session_start();

// Database configuration
$servername = "localhost";
$username = "root";  // Your database username
$password = "";      // Your database password
$database = "ecoswap";  // Your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve user input
$email = $conn->real_escape_string($_POST['email']);
$plainPassword = $_POST['password'];

// Prepare the SQL statement to avoid SQL injection
$stmt = $conn->prepare("SELECT UserID, Password FROM User WHERE Email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    if (password_verify($plainPassword, $row['Password'])) {
        // Password is correct, start user session
        $_SESSION['UserID'] = $row['UserID'];
        header("Location: dashboard.php"); // Redirect to dashboard or home page
    } else {
        // Redirect back to login with an error message
        header("Location: index.php?error=invalidpassword");
    }
} else {
    // Redirect back to login with an error message
    header("Location: index.php?error=invalidemail");
}

$stmt->close();
$conn->close();
?>
