<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // Handle accepting an exchange
    if ($action === 'accept' && isset($_POST['exchangeID'])) {
        // Code to accept the exchange goes here
        // You can implement this based on your database structure
        // Example: update the exchange status to 'Accepted'
        $exchangeID = $_POST['exchangeID'];
        // Update the exchange status in your database
        // Redirect or return a success message
        exit;
    }

    // Handle cancelling a proposed exchange
    if ($action === 'cancel' && isset($_POST['exchangeID'])) {
        // Code to cancel the exchange goes here
        // You can implement this based on your database structure
        // Example: delete the exchange from the database
        $exchangeID = $_POST['exchangeID'];
        // Delete the exchange from your database
        // Redirect or return a success message
        exit;
    }

    // Handle rating a completed exchange
    if ($action === 'rate' && isset($_POST['exchangeID'], $_POST['rating'])) {
        // Code to rate the exchange goes here
        // You can implement this based on your database structure
        // Example: update the user's rating in the database
        $exchangeID = $_POST['exchangeID'];
        $rating = $_POST['rating'];
        // Update the user's rating in your database
        // Redirect or return a success message
        exit;
    }
}

// Fetch incoming exchanges
try {
    // Code to fetch incoming exchanges goes here
    // You can fetch data from your database based on your requirements
    // Example: select exchanges where the user's item is being requested
    // $incomingExchanges = ...;
} catch (PDOException $e) {
    die("Error fetching incoming exchanges: " . $e->getMessage());
}

// Fetch proposed exchanges
try {
    // Code to fetch proposed exchanges goes here
    // You can fetch data from your database based on your requirements
    // Example: select exchanges where the user's item is being offered
    // $proposedExchanges = ...;
} catch (PDOException $e) {
    die("Error fetching proposed exchanges: " . $e->getMessage());
}

// Fetch completed exchanges
try {
    // Code to fetch completed exchanges goes here
    // You can fetch data from your database based on your requirements
    // Example: select exchanges where the status is 'Completed'
    // $completedExchanges = ...;
} catch (PDOException $e) {
    die("Error fetching completed exchanges: " . $e->getMessage());
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
                    <!-- Action button to accept the exchange -->
                    <td><button class="acceptExchange" data-exchangeid="<?= $exchange['ExchangeID'] ?>">Accept</button></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- Proposed Exchanges Table -->
        <h2>Proposed Exchanges</h2>
        <table>
            <!-- Display proposed exchanges similarly -->
        </table>

        <!-- Completed Exchanges Table -->
        <h2>Completed Exchanges</h2>
        <table>
            <!-- Display completed exchanges similarly -->
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $(".acceptExchange").click(function() {
                var exchangeID = $(this).data("exchangeid");
                var confirmAccept = confirm("Are you sure you want to accept this exchange?");
                if (confirmAccept) {
                    // Perform AJAX request to accept the exchange
                    $.ajax({
                        type: "POST",
                        url: "exchange.php",
                        data: { action: "accept", exchangeID: exchangeID },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                alert(response.success);
                                window.location.reload();
                            } else {
                                alert(response.error);
                            }
                        },
                        error: function(xhr) {
                            alert("An error occurred: " + xhr.statusText);
                        }
                    });
                }
            });

            // Similar AJAX functions for cancelling exchanges and submitting ratings
        });
    </script>

</body>
</html>
