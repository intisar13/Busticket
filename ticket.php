<?php
include 'database.php';

// Fetch the last row from the bus_reservation table
$sql = "SELECT * FROM bus_reservation ORDER BY bus_reservation_id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of the last row
    $row = $result->fetch_assoc();

    // Fetch the latest balance from the prepaid_card table
    $card_id = $_GET['card_id'] ?? null;
    if ($card_id) {
        $stmt = $conn->prepare("SELECT balance FROM prepaid_card WHERE card_id = ?");
        $stmt->bind_param("i", $card_id);
        $stmt->execute();
        $stmt->bind_result($latest_balance);
        $stmt->fetch();
        $stmt->close();
    }

    // Fetch the payment method ID
    $payment_method_id = null;
    if ($card_id) {
        $stmt = $conn->prepare("SELECT payment_method_id FROM payment_method WHERE card_id = ?");
        $stmt->bind_param("i", $card_id);
        $stmt->execute();
        $stmt->bind_result($payment_method_id);
        $stmt->fetch();
        $stmt->close();
    }

    // Insert ticket information into tickets table
    if ($row && isset($_GET['balance_change']) && isset($latest_balance) && isset($payment_method_id)) {
        $balance_change = $_GET['balance_change'];
        $new_balance = $latest_balance - $balance_change;

        $stmt = $conn->prepare("INSERT INTO tickets (passenger_id, bus_reservation_id, price, payment_method_id, seats, startingRoute, endingRoute, date, ticketType) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiidissss", $_SESSION['passenger_id'], $row['bus_reservation_id'], $row['price'], $payment_method_id, $row['seats'], $row['startingRoute'], $row['endingRoute'], $row['date'], $row['ticketType']);
        $stmt->execute();
        $stmt->close();
    }
} else {
    $row = null;
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>
    <div class="container">
        <h1>Ticket Information</h1>
        <?php if ($row): ?>
            <p><strong>Reservation ID:</strong> <?php echo $row['bus_reservation_id']; ?></p>
            <p><strong>Ticket Type:</strong> <?php echo $row['ticketType']; ?></p>
            <p><strong>Seats:</strong> <?php echo $row['seats']; ?></p>
            <p><strong>Date:</strong> <?php echo $row['date']; ?></p>
            <p><strong>Starting Route:</strong> <?php echo $row['startingRoute']; ?></p>
            <p><strong>Ending Route:</strong> <?php echo $row['endingRoute']; ?></p>
            <p><strong>Price:</strong> <?php echo $row['price']; ?></p>

        <?php else: ?>
            <p>No reservation found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
