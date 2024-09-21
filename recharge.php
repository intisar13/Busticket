<?php
session_start();
if ($_SESSION['role'] !== 'passenger') {
    header("Location: login.php");
    exit;
}

// Include database connection
include 'database.php';

$message = "";
$card_id = "";
$balance = 0;

// Fetch card details for the logged-in user
$card_details = [];
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session
$stmt = $conn->prepare("SELECT card_id, user_id, balance FROM prepaid_card WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $card_details[] = $row;
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['card_id'])) {
        $card_id = $_POST['card_id'];
        
        // Check if the entered card_id matches any of the user's cards
        $card_ids = array_column($card_details, 'card_id');
        if (in_array($card_id, $card_ids)) {
            if (isset($_POST['show_card'])) {
                // Fetch card details
                $stmt = $conn->prepare("SELECT card_id, balance FROM prepaid_card WHERE card_id = ?");
                $stmt->bind_param("i", $card_id);
                $stmt->execute();
                $stmt->bind_result($card_id, $balance);
                if ($stmt->fetch()) {
                    $message = "Card ID: $card_id, Balance: $balance";
                } else {
                    $message = "Card not found.";
                }
                $stmt->close();
            } elseif (isset($_POST['recharge']) && isset($_POST['amount'])) {
                // Update balance
                $amount = $_POST['amount'];

                $stmt = $conn->prepare("UPDATE prepaid_card SET balance = balance + ? WHERE card_id = ?");
                $stmt->bind_param("ii", $amount, $card_id);
                if ($stmt->execute()) {
                    $message = "Balance updated successfully.";
                } else {
                    $message = "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $message = "Please enter a valid amount.";
            }
        } else {
            $message = "Please select a correct card number from the table.";
        }
    } else {
        $message = "Please enter a valid card number.";
    }
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recharge</title>
    <link rel="stylesheet" href="styles1.css">
</head>
<body>
    <?php
    if (!empty($message)) {
        echo "<p>$message</p>";
    }
    ?>

    <h2>Your Prepaid Cards</h2>
    <table border="1">
        <tr>
            <th>Card ID</th>
            <th>User ID</th>
            <th>Balance</th>
        </tr>
        <?php foreach ($card_details as $card) : ?>
        <tr>
            <td><?php echo htmlspecialchars($card['card_id']); ?></td>
            <td><?php echo htmlspecialchars($card['user_id']); ?></td>
            <td><?php echo htmlspecialchars($card['balance']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <form action="" method="post">
        <div>
            <label for="card_id">Enter Card Number:</label>
            <input type="text" id="card_id" name="card_id" required>
        </div>
        <div>
            <label for="amount">Enter Amount (for Recharge only):</label>
            <input type="number" id="amount" name="amount">
        </div>
        <div>
            <button type="submit" name="show_card">Show Card Details</button>
            <button type="submit" name="recharge">Recharge Card</button>
        </div>
    </form>
    <br>
    <form action="payment_method.php" method="get">
        <button type="submit">Go to Payment Method</button>
    </form>
</body>
</html>

