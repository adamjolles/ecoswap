<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if (isset($_GET['logout'])) {
    // Clear all session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Redirect to the login page
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Connect to the database
$host = 'localhost';
$dbname = 'ecoswap';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error connecting to the database: " . $e->getMessage());
}

// Fetch all items for display
try {
    $stmt = $pdo->query("SELECT ItemID, Title, ItemDescription, ItemCondition FROM Item WHERE UserID != $user_id");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching items: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to EcoSwap</title>
    <link rel="stylesheet" href="pages.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>

    <div class="navbar">
        <a href="dashboard.php">Dashboard</a>
        <a href="items.php">Your Items</a>
        <a href="exchange.php">Exchanges</a>
        <a href="profile.php">Your Profile</a>
        <a href="?logout=true">Logout</a>
    </div>

    <div class="main-content">
        <h1>Welcome to EcoSwap, <?php echo htmlspecialchars($_SESSION['user_email']); ?></h1>
        <p>You are now logged in. Choose an option from the navigation bar.</p>

        <!-- Items Display Section -->
        <div class="item-list">
            <?php if (!empty($items)): ?>
                <h2>Select an item to propose an exchange:</h2>
                <ul>
                    <?php foreach ($items as $item): ?>
                        <li>
                            <?= htmlspecialchars($item['Title']) ?>
                            <form class="exchangeForm" method="post" style="display: inline;">
                                <input type="hidden" name="exchangeItemID" value="<?= $item['ItemID'] ?>">
                                <button type="button" class="exchangeButton">Exchange</button>
                                <div class="exchangePopup" style="display: none;">
                                    <select name="exchangeWithItemID">
                                        <option value="" disabled selected>Select one of your items</option>
                                        <?php
                                        $stmt = $pdo->prepare("SELECT ItemID, Title FROM Item WHERE UserID = ?");
                                        $stmt->execute([$user_id]);
                                        $userItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                        foreach ($userItems as $userItem):
                                        ?>
                                            <option value="<?= $userItem['ItemID'] ?>"><?= htmlspecialchars($userItem['Title']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="proposeExchange">Propose Exchange</button>
                                </div>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No items found.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $(".exchangeButton").click(function() {
                $(this).siblings(".exchangePopup").show();
            });

            $(".exchangeForm").submit(function(event) {
                event.preventDefault();
                var form = $(this);
                $.ajax({
                    type: "POST",
                    url: "process_exchange.php", // Update to your PHP script handling exchange proposals
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.success);
                            form.find(".exchangePopup").hide();
                            window.location.reload();
                        } else {
                            alert(response.error);
                        }
                    },
                    error: function(xhr) {
                        alert("An error occurred: " + xhr.statusText);
                    }
                });
            });
        });
    </script>

</body>
</html>
