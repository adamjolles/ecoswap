<?php
// Assuming you have already created your database and User table

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

// Retrieve user input from form
$name = $conn->real_escape_string($_POST['name']);
$email = $conn->real_escape_string($_POST['email']);
$plainPassword = $_POST['password'];

// Hash the password
$passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);

// Prepare the SQL statement to avoid SQL injection
$stmt = $conn->prepare("INSERT INTO User (Name, Email, Password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $passwordHash);

// Execute the statement
if ($stmt->execute()) {
    echo "Registration successful!";
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
