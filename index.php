<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EcoSwap - Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .container {
            width: 300px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
        }
        input[type="email"],
        input[type="password"] {
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"],
        a {
            padding: 10px;
            display: block;
            text-align: center;
            background-color: #008000;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
        }
        a:hover {
            background-color: #005700;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login to EcoSwap</h2>
        <form action="process_login.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Login">
        </form>
        <a href="register.php">Create an account</a>
    </div>
</body>
</html>
