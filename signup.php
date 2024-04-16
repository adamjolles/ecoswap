<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - EcoSwap</title>
</head>
<body>
    <h1>Create an Account for EcoSwap</h1>
    <?php if (isset($_GET['error'])): ?>
        <p style="color: red;">
            <?php
            switch ($_GET['error']) {
                case 'email_taken':
                    echo "This email is already taken. Please use a different one.";
                    break;
                case 'missing_fields':
                    echo "Please fill in all the fields.";
                    break;
                case 'password_mismatch':
                    echo "The passwords do not match. Please try again.";
                    break;
                default:
                    echo "An unexpected error occurred. Please try again.";
                    break;
            }
            ?>
        </p>
    <?php endif; ?>
    <form action="process_signup.php" method="post">
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit">Sign Up</button>
    </form>
</body>
</html>
