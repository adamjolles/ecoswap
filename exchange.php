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

    // Fetch incoming exchanges
    $incomingStmt = $pdo->prepare("SELECT E.ExchangeID, E.Item1ID, E.Item2ID, E.Status, I1.Title AS YourItemTitle, I2.Title AS OtherUserItemTitle
                                    FROM Exchange E
                                    INNER JOIN Item I1 ON E.Item1ID = I1.ItemID
                                    INNER JOIN Item I2 ON E.Item2ID = I2.ItemID
                                    WHERE I2.UserID = ? AND E.Status = 'Pending'");
    $incomingStmt->execute([$user_id]);
    $incomingExchanges = $incomingStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch proposed exchanges
    $proposedStmt = $pdo->prepare("SELECT E.ExchangeID, E.Item1ID, E.Item2ID, E.Status, I1.Title AS YourItemTitle, I2.Title AS OtherUserItemTitle
                                    FROM Exchange E
                                    INNER JOIN Item I1 ON E.Item1ID = I1.ItemID
                                    INNER JOIN Item I2 ON E.Item2ID = I2.ItemID
                                    WHERE I1.UserID = ? AND E.Status = 'Pending'");
    $proposedStmt->execute([$user_id]);
    $proposedExchanges = $proposedStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch completed exchanges
    $completedStmt = $pdo->prepare("SELECT E.ExchangeID, E.Item1ID, E.Item2ID, E.Status, I1.Title AS YourItemTitle, I2.Title AS OtherUserItemTitle
                                    FROM Exchange E
                                    INNER JOIN Item I1 ON E.Item1ID = I1.ItemID
                                    INNER JOIN Item I2 ON E.Item2ID = I2.ItemID
                                    WHERE (I1.UserID = ? OR I2.UserID = ?) AND E.Status = 'Accepted'");
    $completedStmt->execute([$user_id, $user_id]);
    $completedExchanges = $completedStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error fetching exchanges: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Exchanges - EcoSwap</title>
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
        <h1>Your Exchanges</h1>

        <!-- Incoming Exchanges Table -->
        <h2>Incoming Exchanges</h2>
        <table>
            <tr>
                <th>Exchange ID</th>
                <th>Status</th>
                <th>Your Item</th>
                <th>Other User's Item</th>
                <th>Action</th>
            </tr>
            <?php foreach ($incomingExchanges as $exchange): ?>
                <tr>
                    <!-- Display exchange details -->
                    <td><?= $exchange['ExchangeID'] ?></td>
                    <td><?= $exchange['Status'] ?></td>
                    <td><?= $exchange['YourItemTitle'] ?></td>
                    <td><?= $exchange['OtherUserItemTitle'] ?></td>
                    <td><button class="acceptExchange" data-exchangeid="<?= $exchange['ExchangeID'] ?>">Accept</button></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- Proposed Exchanges Table -->
        <h2>Proposed Exchanges</h2>
        <table>
            <tr>
                <th>Exchange ID</th>
                <th>Status</th>
                <th>Your Item</th>
                <th>Other User's Item</th>
                <th>Action</th>
            </tr>
            <?php foreach ($proposedExchanges as $exchange): ?>
                <tr>
                    <!-- Display exchange details -->
                    <td><?= $exchange['ExchangeID'] ?></td>
                    <td><?= $exchange['Status'] ?></td>
                    <td><?= $exchange['YourItemTitle'] ?></td>
                    <td><?= $exchange['OtherUserItemTitle'] ?></td>
                    <td><button class="cancelExchange" data-exchangeid="<?= $exchange['ExchangeID'] ?>">Cancel</button></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- Completed Exchanges Table -->
        <h2>Completed Exchanges</h2>
        <table>
            <tr>
                <th>Exchange ID</th>
                <th>Status</th>
                <th>Your Item</th>
                <th>Other User's Item</th>
                <th>Action</th>
                <th>Rating</th>
            </tr>
            <?php foreach ($completedExchanges as $exchange): ?>
                <tr>
                    <!-- Display exchange details -->
                    <td><?= $exchange['ExchangeID'] ?></td>
                    <td><?= $exchange['Status'] ?></td>
                    <td><?= $exchange['YourItemTitle'] ?></td>
                    <td><?= $exchange['OtherUserItemTitle'] ?></td>
                    <td><button class="rateUser" data-userid="<?= $exchange['OtherUserID'] ?>">Rate</button></td>
                    <td><?= calculateAverageRating($exchange['OtherUserID']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <script>
        $(document).ready(function () {
            $(".acceptExchange").click(function () {
                var exchangeID = $(this).data("exchangeid");
                var confirmAccept = confirm("Are you sure you want to accept this exchange?");
                if (confirmAccept) {
                    $.ajax({
                        type: "POST",
                        url: "process_exchange.php",
                        data: { action: "accept", exchangeID: exchangeID },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                alert(response.success);
                                window.location.reload();
                            } else {
                                alert(response.error);
                            }
                        },
                        error: function (xhr) {
                            alert("An error occurred: " + xhr.statusText);
                        }
                    });
                }
            });

            $(".rateUser").click(function () {
                var ratedUserID = $(this).data("userid");
                var rating = prompt("Please enter your rating for this user (1-5):");
                if (rating !== null && rating !== '' && !isNaN(rating) && rating >= 1 && rating <= 5) {
                    $.ajax({
                        type: "POST",
                        url: "process_review.php",
                        data: { exchangeID: ratedUserID, rating: rating },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                alert(response.success);
                                window.location.reload();
                            } else {
                                alert(response.error);
                            }
                        },
                        error: function (xhr) {
                            alert("An error occurred: " + xhr.statusText);
                        }
                    });
                } else {
                    alert("Please enter a valid rating between 1 and 5.");
                }
            });

            $(".cancelExchange").click(function () {
                var exchangeID = $(this).data("exchangeid");
                var confirmCancel = confirm("Are you sure you want to cancel this exchange?");
                if (confirmCancel) {
                    $.ajax({
                        type: "POST",
                        url: "process_exchange.php",
                        data: { action: "cancel", exchangeID: exchangeID },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                alert(response.success);
                                window.location.reload();
                            } else {
                                alert(response.error);
                            }
                        },
                        error: function (xhr) {
                            alert("An error occurred: " + xhr.statusText);
                        }
                    });
                }
            });
        });
    </script>

</body>

</html>