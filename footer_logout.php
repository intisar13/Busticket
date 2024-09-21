<?php
// logout.php and footer combined
session_start();

// If logout request is detected
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// Check if session is not already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Rest of the code for logout button
echo '<form action="logout.php" method="post">
        <button type="submit">Logout</button>
      </form>';
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Footer</title>
</head>
<body>

    <footer>
        <form action="" method="post">
            <input type="submit" name="logout" value="Logout">
        </form>
    </footer>
</body>
</html>
