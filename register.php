<?php
include("database.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $role = filter_input(INPUT_POST, "role", FILTER_SANITIZE_SPECIAL_CHARS);
    $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_SPECIAL_CHARS);
    $age = filter_input(INPUT_POST, "age", FILTER_VALIDATE_INT);
    $gender = filter_input(INPUT_POST, "gender", FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($username) || empty($password) || empty($email) || empty($role) || empty($name) || empty($age) || empty($gender)) {
        echo "All fields are required!";
    } else {
        // Check if the username already exists
        $stmt = mysqli_prepare($conn, "SELECT user_id FROM user_account WHERE username = ?");
        if (!$stmt) {
            die("Failed to prepare statement: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "s", $username);
        if (!mysqli_stmt_execute($stmt)) {
            die("Failed to execute statement: " . mysqli_stmt_error($stmt));
        }

        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            echo "Username already exists!";
        } else {
            mysqli_stmt_close($stmt);

            // Insert the new user into the user_account table
            $stmt = mysqli_prepare($conn, "INSERT INTO user_account (username, password, email, role) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                die("Failed to prepare statement: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, "ssss", $username, $password, $email, $role);
            if (!mysqli_stmt_execute($stmt)) {
                die("Failed to execute statement: " . mysqli_stmt_error($stmt));
            }

            // Get the inserted user_id
            $user_id = mysqli_insert_id($conn);

            // Depending on the role, insert into respective tables
            if ($role === 'passenger') {
                $table = 'passenger';
            } elseif ($role === 'staff') {
                $table = 'staff';
            }

            // Insert into the respective role table
            $stmt = mysqli_prepare($conn, "INSERT INTO $table (user_id, name, age, gender) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                die("Failed to prepare statement: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, "isis", $user_id, $name, $age, $gender);
            if (!mysqli_stmt_execute($stmt)) {
                die("Failed to execute statement: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);

            // Insert into the prepaid_card table if role is passenger with a balance of 100
            if ($role === 'passenger') {
                $stmt = mysqli_prepare($conn, "INSERT INTO prepaid_card (user_id, balance) VALUES (?, ?)");
                if (!$stmt) {
                    die("Failed to prepare statement: " . mysqli_error($conn));
                }

                $balance = 100;
                mysqli_stmt_bind_param($stmt, "ii", $user_id, $balance);
                if (!mysqli_stmt_execute($stmt)) {
                    die("Failed to execute statement: " . mysqli_stmt_error($stmt));
                }
                mysqli_stmt_close($stmt);
            }

            echo "You are registered!";
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="styles1.css">
</head>
<body>
    <form action="register.php" method="post">
        <h2>Register</h2>
        Role:<br>
        <input type="radio" id="passenger" name="role" value="passenger" checked>
        <label for="passenger">Passenger</label>
        <input type="radio" id="staff" name="role" value="staff">
        <label for="staff">Staff</label>
        <input type="radio" id="admin" name="role" value="admin">
        <label for="admin">Admin</label><br><br>

        Username:<br>
        <input type="text" name="username" required><br>
        Password:<br>
        <input type="password" name="password" required><br>
        Email:<br>
        <input type="email" name="email" required><br>
        Name:<br>
        <input type="text" name="name" required><br>
        Age:<br>
        <input type="number" name="age" required><br>
        Gender:<br>
        <input type="text" name="gender" required><br>

        <input type="submit" value="Register">
    </form>

    <div class="weak">
    <div class="row">
    <a href="login.php" class="btn">CLICK HERE TO LOGIN Now</a>
  </div>
  </div>
</body>
</html>


