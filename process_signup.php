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

// Check if Name, Email, and Password are set and the passwords match
if (isset($_POST['name'], $_POST['email'], $_POST['password'], $_POST['confirm_password'])) {
    if ($_POST['password'] !== $_POST['confirm_password']) {
        // Redirect back to the signup page if the passwords do not match
        header("Location: signup.php?error=password_mismatch");
        exit;
    }

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Prepare and execute the insert query
    try {
        $stmt = $pdo->prepare("INSERT INTO User (Name, Email, Password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $password]);
        
        // Redirect to the sign-in page after successful sign-up
        header("Location: index.php?signup=success");
        exit;
    } catch (PDOException $e) {
        // Handle potential errors, like duplicate entries
        if ($e->getCode() == 23000) {
            // Duplicate entry
            header("Location: signup.php?error=email_taken");
        } else {
            die("Error creating the account: " . $e->getMessage());
        }
        exit;
    }
} else {
    // Redirect back to the signup page with an error message
    header("Location: signup.php?error=missing_fields");
    exit;
}
?>
