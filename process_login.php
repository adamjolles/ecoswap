<?php
session_start();

// Connect to the database
$host = 'localhost'; // Or the appropriate host IP
$dbname = 'ecoswap';
$user = 'root'; // Default XAMPP username
$pass = ''; // Default XAMPP password is blank

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $dbname :" . $e->getMessage());
}

// Check if Email and Password are set
if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute the query
    $stmt = $pdo->prepare("SELECT * FROM User WHERE Email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Verify password and start the session
    if ($user && password_verify($password, $user['Password'])) {
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['user_email'] = $user['Email'];
        // Redirect to a welcome or home page after successful login
        header("Location: dashboard.php");
        exit;
    } else {
        // Redirect back to the login page with an error message
        header("Location: index.php?error=invalid_credentials");
        exit;
    }
} else {
    // Redirect back to the login page with an error message
    header("Location: index.php?error=empty_fields");
    exit;
}
?>
