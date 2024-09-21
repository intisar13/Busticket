<?php
session_start();
include 'database.php'; // Include database connection

// Function to toggle session variable
function toggleSession(&$sessionVar) {
    $sessionVar = !isset($sessionVar) || !$sessionVar;
}

// Check user role
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Toggle session variables
if (isset($_POST['show_buses'])) {
    toggleSession($_SESSION['show_buses']);
}

if (isset($_POST['show_bus_company'])) {
    toggleSession($_SESSION['show_bus_company']);
}

if (isset($_POST['show_bus_reservation'])) {
    toggleSession($_SESSION['show_bus_reservation']);
}

if (isset($_POST['show_discounts'])) {
    toggleSession($_SESSION['show_discounts']);
}

if (isset($_POST['show_drivers'])) {
    toggleSession($_SESSION['show_drivers']);
}

if (isset($_POST['show_fare_rules'])) {
    toggleSession($_SESSION['show_fare_rules']);
}

if (isset($_POST['show_passenger'])) {
    toggleSession($_SESSION['show_passenger']);
}

if (isset($_POST['show_payment_method'])) {
    toggleSession($_SESSION['show_payment_method']);
}

if (isset($_POST['show_prepaid_card'])) {
    toggleSession($_SESSION['show_prepaid_card']);
}

if (isset($_POST['show_user_account'])) {
    toggleSession($_SESSION['show_user_account']);
}

// Fetch data from the database if the session variable is set
if (isset($_SESSION['show_buses']) && $_SESSION['show_buses']) {
    $result_buses = $conn->query("SELECT * FROM buses");
}

if (isset($_SESSION['show_bus_company']) && $_SESSION['show_bus_company']) {
    $result_bus_company = $conn->query("SELECT * FROM bus_company");
}

if (isset($_SESSION['show_bus_reservation']) && $_SESSION['show_bus_reservation']) {
    $result_bus_reservation = $conn->query("SELECT * FROM bus_reservation");
}

if (isset($_SESSION['show_discounts']) && $_SESSION['show_discounts']) {
    $result_discounts = $conn->query("SELECT * FROM discounts");
}

if (isset($_SESSION['show_drivers']) && $_SESSION['show_drivers']) {
    $result_drivers = $conn->query("SELECT * FROM drivers");
}

if (isset($_SESSION['show_fare_rules']) && $_SESSION['show_fare_rules']) {
    $result_fare_rules = $conn->query("SELECT * FROM fare_rules");
}

if (isset($_SESSION['show_passenger']) && $_SESSION['show_passenger']) {
    $result_passenger = $conn->query("SELECT * FROM passenger");
}

if (isset($_SESSION['show_payment_method']) && $_SESSION['show_payment_method']) {
    $result_payment_method = $conn->query("SELECT * FROM payment_method");
}

if (isset($_SESSION['show_prepaid_card']) && $_SESSION['show_prepaid_card']) {
    $result_prepaid_card = $conn->query("SELECT * FROM prepaid_card");
}

if (isset($_SESSION['show_user_account']) && $_SESSION['show_user_account']) {
    $result_user_account = $conn->query("SELECT * FROM user_account");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Home</title>
    <style>
        .hidden {
            display: none;
        }
    </style>
    <link rel="stylesheet" href="styles1.css">
</head>
<body>
    <h1>Welcome, Admin!</h1>

    <!-- Button to toggle buses display -->
    <form action="" method="post">
        <input type="submit" name="show_buses" value="<?php echo isset($_SESSION['show_buses']) && $_SESSION['show_buses'] ? 'Hide Buses' : 'Show Buses'; ?>">
    </form>

    <?php
    // Display buses data if the button is clicked and session variable is set
    if (isset($_SESSION['show_buses']) && $_SESSION['show_buses']) {
        displayData($result_buses, 'Buses', ['Bus ID', 'Bus Number', 'Capacity', 'Bus Company ID', 'Driver ID', 'Route ID']);
    }
    ?>

    <!-- Button to toggle bus company display -->
    <form action="" method="post">
        <input type="submit" name="show_bus_company" value="<?php echo isset($_SESSION['show_bus_company']) && $_SESSION['show_bus_company'] ? 'Hide Bus Company' : 'Show Bus Company'; ?>">
    </form>

    <?php
    // Display bus company data if the button is clicked and session variable is set
    if (isset($_SESSION['show_bus_company']) && $_SESSION['show_bus_company']) {
        displayData($result_bus_company, 'Bus Company', ['Bus Company ID', 'Company Name', 'Contact Info', 'Bus ID']);
    }
    ?>

    <!-- Button to toggle bus reservation display -->
    <form action="" method="post">
        <input type="submit" name="show_bus_reservation" value="<?php echo isset($_SESSION['show_bus_reservation']) && $_SESSION['show_bus_reservation'] ? 'Hide Bus Reservation' : 'Show Bus Reservation'; ?>">
    </form>

    <?php
    // Display bus reservation data if the button is clicked and session variable is set
    if (isset($_SESSION['show_bus_reservation']) && $_SESSION['show_bus_reservation']) {
        displayData($result_bus_reservation, 'Bus Reservation', ['Bus Reservation ID', 'Ticket Type', 'Seats', 'Date', 'Starting Route', 'Ending Route', 'Price']);
    }
    ?>

    <!-- Button to toggle discounts display -->
    <form action="" method="post">
        <input type="submit" name="show_discounts" value="<?php echo isset($_SESSION['show_discounts']) && $_SESSION['show_discounts'] ? 'Hide Discounts' : 'Show Discounts'; ?>">
    </form>

    <?php
    // Display discounts data if the button is clicked and session variable is set
    if (isset($_SESSION['show_discounts']) && $_SESSION['show_discounts']) {
        displayData($result_discounts, 'Discounts', ['Discount ID', 'Description', 'Percentage', 'Bus Company ID']);
    }
    ?>

    <!-- Button to toggle drivers display -->
    <form action="" method="post">
        <input type="submit" name="show_drivers" value="<?php echo isset($_SESSION['show_drivers']) && $_SESSION['show_drivers'] ? 'Hide Drivers' : 'Show Drivers'; ?>">
    </form>

    <?php
    // Display drivers data if the button is clicked and session variable is set
    if (isset($_SESSION['show_drivers']) && $_SESSION['show_drivers']) {
        displayData($result_drivers, 'Drivers', ['Driver ID', 'Name', 'License Number', 'Phone Number', 'Qualifications', 'Training', 'Awards', 'Bus ID']);
    }
    ?>

    <!-- Button to toggle fare rules display -->
    <form action="" method="post">
        <input type="submit" name="show_fare_rules" value="<?php echo isset($_SESSION['show_fare_rules']) && $_SESSION['show_fare_rules'] ? 'Hide Fare Rules' : 'Show Fare Rules'; ?>">
    </form>

    <?php
    // Display fare rules data if the button is clicked and session variable is set
    if (isset($_SESSION['show_fare_rules']) && $_SESSION['show_fare_rules']) {
        displayData($result_fare_rules, 'Fare Rules', ['Fare Rule ID', 'Fare Rule Follow', 'Route ID']);
    }
    ?>

    <!-- Button to toggle passenger display -->
    <form action="" method="post">
        <input type="submit" name="show_passenger" value="<?php echo isset($_SESSION['show_passenger']) && $_SESSION['show_passenger'] ? 'Hide Passenger' : 'Show Passenger'; ?>">
    </form>

    <?php
    // Display passenger data if the button is clicked and session variable is set
    if (isset($_SESSION['show_passenger']) && $_SESSION['show_passenger']) {
        displayData($result_passenger, 'Passenger', ['Passenger ID', 'User ID', 'Name', 'Age', 'Gender', 'Bus Reservation ID', 'Refund ID']);
    }
    ?>

    <!-- Button to toggle payment method display -->
    <form action="" method="post">
        <input type="submit" name="show_payment_method" value="<?php echo isset($_SESSION['show_payment_method']) && $_SESSION['show_payment_method'] ? 'Hide Payment Method' : 'Show Payment Method'; ?>">
    </form>

    <?php
    // Display payment method data if the button is clicked and session variable is set
    if (isset($_SESSION['show_payment_method']) && $_SESSION['show_payment_method']) {
        displayData($result_payment_method, 'Payment Method', ['Payment Method ID', 'Method Name', 'Card ID', 'Passenger ID']);
    }
    ?>

    <!-- Button to toggle prepaid card display -->
    <form action="" method="post">
        <input type="submit" name="show_prepaid_card" value="<?php echo isset($_SESSION['show_prepaid_card']) && $_SESSION['show_prepaid_card'] ? 'Hide Prepaid Card' : 'Show Prepaid Card'; ?>">
    </form>

    <?php
    // Display prepaid card data if the button is clicked and session variable is set
    if (isset($_SESSION['show_prepaid_card']) && $_SESSION['show_prepaid_card']) {
        displayData($result_prepaid_card, 'Prepaid Card', ['Card ID', 'Balance', 'Passenger ID', 'Method ID']);
    }
    ?>

    <!-- Button to toggle user account display -->
    <form action="" method="post">
        <input type="submit" name="show_user_account" value="<?php echo isset($_SESSION['show_user_account']) && $_SESSION['show_user_account'] ? 'Hide User Account' : 'Show User Account'; ?>">
    </form>

    <?php
    // Display user account data if the button is clicked and session variable is set
    if (isset($_SESSION['show_user_account']) && $_SESSION['show_user_account']) {
        displayData($result_user_account, 'User Account', ['User ID', 'Username', 'Password', 'Email', 'Role', 'Passenger ID', 'Staff ID']);
    }
    ?>

    <?php include 'footer_logout.php'; ?>
</body>
</html>

<?php
// Function to display data in a table
function displayData($result, $title, $columns) {
    if ($result->num_rows > 0) {
        echo "<h2>$title</h2>";
        echo "<table border='1'>";
        echo "<tr>";
        foreach ($columns as $column) {
            echo "<th>$column</th>";
        }
        echo "</tr>";
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>$value</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No $title found.</p>";
    }
}
?>



