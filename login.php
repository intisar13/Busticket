<?php
include("database.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($username) || empty($password)) {
        echo "All fields are required!";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT user_id, password, role FROM user_account WHERE username = ?");
        if (!$stmt) {
            die("Failed to prepare statement: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "s", $username);
        if (!mysqli_stmt_execute($stmt)) {
            die("Failed to execute statement: " . mysqli_stmt_error($stmt));
        }

        mysqli_stmt_bind_result($stmt, $user_id, $stored_password, $stored_role);
        mysqli_stmt_fetch($stmt);

        if ($password === $stored_password) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user_id; // Store user_id in session
            $_SESSION['role'] = $stored_role;

            switch ($stored_role) {
                case 'passenger':
                    header("Location: passenger.php");
                    break;
                case 'staff':
                    header("Location: staff.php");
                    break;
                case 'admin':
                    header("Location: admin.php");
                    break;
            }
            exit;
        } else {
            echo "Invalid username or password!";
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="styles1.css">
</head>
<body>
    <form action="login.php" method="post">
        <h2>Login</h2>
        Username:<br>
        <input type="text" name="username" required><br>
        Password:<br>
        <input type="password" name="password" required><br>
        <input type="submit" value="Login">
    </form>
    <div class="weak">
    <a href="register.php">CLICK HERE TO REGISTER</a>
    </div>
</body>
</html>

