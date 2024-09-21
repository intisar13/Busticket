<?php
session_start();
if ($_SESSION['role'] !== 'passenger') {
    header("Location: login.php");
    exit;
}

// Include database connection
include 'database.php';

$message = "";

// Fetch the ticket price from the latest reservation
$ticket_price = 0;
$result = $conn->query("SELECT price FROM bus_reservation ORDER BY bus_reservation_id DESC LIMIT 1");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $ticket_price = $row['price'];
}

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

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['payment_method']) && isset($_POST['card_id'])) {
        $payment_method = $_POST['payment_method'];
        $card_id = $_POST['card_id'];
        
        // Check if the entered card_id matches any of the user's cards
        $card_ids = array_column($card_details, 'card_id');
        if (in_array($card_id, $card_ids)) {
            // Fetch card balance
            $stmt = $conn->prepare("SELECT balance FROM prepaid_card WHERE card_id = ?");
            $stmt->bind_param("i", $card_id);
            $stmt->execute();
            $stmt->bind_result($balance);
            if ($stmt->fetch()) {
                if ($balance >= $ticket_price) {
                    // Deduct the ticket price from the balance
                    $new_balance = $balance - $ticket_price;
                    $stmt->close();

                    // Update the card balance
                    $stmt = $conn->prepare("UPDATE prepaid_card SET balance = ? WHERE card_id = ?");
                    $stmt->bind_param("ii", $new_balance, $card_id);
                    if ($stmt->execute()) {
                        // Insert the payment method
                        $stmt->close();
                        $stmt = $conn->prepare("INSERT INTO payment_method (method_name, card_id) VALUES (?, ?)");
                        $stmt->bind_param("si", $payment_method, $card_id);
                        if ($stmt->execute()) {
                            // Redirect to ticket.php after successful insertion
                            header("Location: ticket.php?card_id=" . $card_id . "&balance_change=" . $ticket_price);
                            exit;
                        } else {
                            $message = "Error: " . $stmt->error;
                        }
                    } else {
                        $message = "Error updating balance: " . $stmt->error;
                    }
                } else {
                    $message = "Insufficient balance.";
                }
            } else {
                $message = "Card not found.";
            }
            $stmt->close();
        } else {
            $message = "Please select a correct card number from the table.";
        }
    } else {
        $message = "Please select a payment method and enter a card number.";
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
    <title>Payment Method</title>
    <link rel="stylesheet" href="styles1.css">

</head>
<body>
    <?php
    if (!empty($message)) {
        echo "<p>$message</p>";
    }
    ?>

<div id="pmcss">
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
        <fieldset>
            <legend>Select Payment Method:</legend>
            <input type="radio" id="bkash" name="payment_method" value="bkash" required>
            <label for="bkash">Bkash</label><br>
            <input type="radio" id="nagad" name="payment_method" value="nagad">
            <label for="nagad">Nagad</label><br>
            <input type="radio" id="prepaid_card" name="payment_method" value="prepaid_card">
            <label for="prepaid_card">Prepaid Card</label><br>
        </fieldset>
        <div>
            <label for="card_id">Enter Card Number:</label>
            <input type="text" id="card_id" name="card_id" required>
        </div>
        <input type="submit" value="Submit">
    </form>

    <form action="recharge.php" method="post">
        <input type="submit" value="Recharge">
    </form>
        </div>
</body>
</html>
