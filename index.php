<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EcoSwap</title>
    <link rel="stylesheet" href="pages.css">
</head>

<body>
    <h1>Welcome to EcoSwap</h1>
    <?php if (isset($_GET['error'])): ?>
        <p style="color: red;">
            <?php if ($_GET['error'] == 'invalid_credentials'): ?>
                Invalid email or password.
            <?php elseif ($_GET['error'] == 'empty_fields'): ?>
                Please fill in all the fields.
            <?php endif; ?>
        </p>
    <?php endif; ?>
    <form action="process_login.php" method="post">
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Sign In</button>

    </form>
    <h2>Need an account?</h2>
    <a href="signup.php">Sign up here</a>
</body>

</html>