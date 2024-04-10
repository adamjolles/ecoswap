<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "ecoswap";

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
$passwordHash = password_hash($plainPassword, "PASSWORD");

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
