<?php
session_start();
if ($_SESSION['role'] !== 'passenger') {
    header("Location: login.php");
    exit;
}

// Include database connection
include 'database.php';

// Function to calculate the price
function calculatePrice($ticketType, $seats) {
    // Define the price per ticket based on ticket type
    $pricePerTicket = ($ticketType === 'Premium') ? 500 : 200;

    // Calculate the total price
    $totalPrice = $pricePerTicket * $seats;

    return $totalPrice;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ticketType = $_POST["Ticket_type"];
    $seats = $_POST["Seats"];
    $date = $_POST["Date"];
    $startingRoute = $_POST["Starting_Route"];
    $endingRoute = $_POST["Ending_Route"];
    $userId = $_SESSION['user_id']; // Retrieve the user_id from the session

    // Calculate the total price
    $price = calculatePrice($ticketType, $seats);

    echo "Ticket Type: $ticketType <br>";
    echo "Seats: $seats <br>";
    echo "Date: $date <br>";
    echo "Starting Route: $startingRoute <br>";
    echo "Ending Route: $endingRoute <br>";
    echo "Price: $price <br>";

    // Insert ticket information into the database
    $stmt_insert_reservation = $conn->prepare("INSERT INTO bus_reservation (user_id, ticketType, seats, date, startingRoute, endingRoute, price) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt_insert_reservation) {
        echo "Error: " . $conn->error;
    } else {
        // Bind parameters
        $stmt_insert_reservation->bind_param("isissii", $userId, $ticketType, $seats, $date, $startingRoute, $endingRoute, $price);
        
        // Execute the statement
        if ($stmt_insert_reservation->execute()) {
            // Get the generated bus_reservation_id
            $bus_reservation_id = $stmt_insert_reservation->insert_id;

            // Close the statement
            $stmt_insert_reservation->close();

            // Check if there are existing entries for the user in the passenger table
            $stmt_check_passenger = $conn->prepare("SELECT * FROM passenger WHERE user_id = ?");
            $stmt_check_passenger->bind_param("i", $userId);
            $stmt_check_passenger->execute();
            $result_check_passenger = $stmt_check_passenger->get_result();
            $stmt_check_passenger->close();

            if ($result_check_passenger->num_rows > 0) {
                // If there are existing entries, update the latest entry with the new bus_reservation_id
                $stmt_update_passenger = $conn->prepare("UPDATE passenger SET bus_reservation_id = ? WHERE user_id = ? ORDER BY passenger_id DESC LIMIT 1");
                $stmt_update_passenger->bind_param("ii", $bus_reservation_id, $userId);
                $stmt_update_passenger->execute();
                $stmt_update_passenger->close();
            } else {
                // If there are no existing entries, insert a new row into the passenger table
                $stmt_insert_passenger = $conn->prepare("INSERT INTO passenger (user_id, bus_reservation_id) VALUES (?, ?)");
                $stmt_insert_passenger->bind_param("ii", $userId, $bus_reservation_id);
                $stmt_insert_passenger->execute();
                $stmt_insert_passenger->close();
            }

            // Redirect to payment_method.php
            header("Location: payment_method.php");
            exit;
        } else {
            echo "Error: " . $stmt_insert_reservation->error;
        }
    }
    
    // Close the connection
    $conn->close();
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Passenger Home</title>
    <link rel="stylesheet" href="passenger.css">
</head>
<body>
    <h1>Welcome, Passenger!</h1>
    
    <nav>
        <div class="dropdown">
            <button><a href="#" class="home">HOME</a></button>
            <div class="Products">
                <button>BUS</button>
                <ul>
                <li><a href="bus1.php"></a>BUS 1</a></li>
                    <li><a href="#">BUS 2</a></li>
                    <li><a href="#">BUS 3</a></li>
                </ul>
            </div>
            <div class="Services">
                <button>BUS COMPANY</button>
                <ul>
                    <li><a href="#">COMPANY 1</a></li>
                </ul>
            </div>
            <div class="Routes">
                <button>ROUTES</button>
                <ul>
                    <li><a href="#">Route 1</a></li>
                    <li><a href="#">Route 2</a></li>
                    <li><a href="#">Route 3</a></li>
                </ul>
            </div>
            <div class="Routes">
                <button>LOGOUT</button>
                <ul>
                <li><button><a href="logout.php">logout</a></button></li>
                    
                </ul>
            </div>
        </div>
    </nav>
   

    <form action="passenger.php" method="post">
        <div class="main">
            <label for="Ticket_type">Ticket Type: </label>
            <label for="Premium">Premium</label>
            <input type="radio" id="Premium" name="Ticket_type" value="Premium">
            <label for="Standard">Standard</label>
            <input type="radio" id="Standard" name="Ticket_type" value="Standard">
        </div>

        <div class="Seat">
            <label for="Seats">Seats</label>
            <input type="number" id="Seats" name="Seats">
        </div>

        <div class="Date">
            <label for="Date">Date</label>
            <input type="date" id="Date" name="Date">
        </div>

        <div class="routes">
            <label for="Starting_Route">Starting Route</label>
            <select id="Starting_Route" name="Starting_Route">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
            <br>
            <label for="Ending_Route">Ending Route</label>
            <select id="Ending_Route" name="Ending_Route">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
            <br>
            <input type="submit" value="Proceed to Payment">
        </div>
    </form>

    <footer>
    <?php
    try {
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            // Check if the warning is about an already active session and ignore it
            if (strpos($errstr, 'session_start(): Ignoring session_start() because a session is already active') === false) {
                throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
            }
        });

        include 'footer_logout.php';

        restore_error_handler();
    } catch (Exception $e) {
        // Handle other exceptions here
        echo 'An error occurred while including the footer: ',  $e->getMessage();
    }
    ?>
</footer>


</body>
</html>