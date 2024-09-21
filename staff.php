<?php
session_start();
if ($_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit;
}

include 'database.php';

$staffInfo = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_staff_info'])) {
    $sql = "SELECT s.staff_id, s.user_id, s.name, s.age, s.gender, s.bus_id FROM staff s
            INNER JOIN user_account u ON s.user_id = u.user_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $staffInfo = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $staffInfo = [];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Home</title>
    <link rel="stylesheet" href="staff.css">
    
</head>
<body>
    <h1>Welcome, Staff!</h1>
    <nav>
        <div class="dropdown">
            <button><a href="#" class="home">HOME</a></button>
            <div class="Products">
                <button>BUS</button>
                <ul>
                    <li><a href="#">BUS 1</a></li>
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
                    <li><?php include 'footer_logout.php'; ?></li>
                </ul>
            </div>
        </div>
    </nav>

    <form method="post">
        <input type="submit" name="check_staff_info" value="Check Staff Information">
    </form>

    <?php if ($staffInfo !== null): ?>
        <?php if (!empty($staffInfo)): ?>
            <table border="1">
                <tr>
                    <th>Staff ID</th>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Bus ID</th>
                </tr>
                <?php foreach ($staffInfo as $staff): ?>
                    <tr>
                        <td><?php echo $staff['staff_id']; ?></td>
                        <td><?php echo $staff['user_id']; ?></td>
                        <td><?php echo $staff['name']; ?></td>
                        <td><?php echo $staff['age']; ?></td>
                        <td><?php echo $staff['gender']; ?></td>
                        <td><?php echo $staff['bus_id']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?php include 'footer_logout.php'; ?>
        <?php else: ?>
            <p>No staff information found.</p>
        <?php endif; ?>
    <?php endif; ?>

    
</body>
</html>
