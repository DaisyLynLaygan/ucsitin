<?php
session_start();
include 'config.php'; // Database connection

// Example: Fetch sit-in history from the database
$user_id = $_SESSION['user_id']; // Assuming user ID is stored in session
$query = "SELECT date, time, status FROM sit_in_history WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
/*
<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "ccs_sitin";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sit-in History</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: whitesmoke;
            margin: 0;
            padding: 0;
        }
        .container {
            margin-left: 270px;
            padding: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .table th, .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .table th {
            background-color: purple;
            color: white;
        }
        .status {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .completed {
            background-color: green;
            color: white;
        }
        .pending {
            background-color: orange;
            color: white;
        }
        .cancelled {
            background-color: red;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Sit-in History</h2>
        <table class="table">
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['date']; ?></td>
                <td><?php echo $row['time']; ?></td>
                <td>
                    <span class="status <?php echo strtolower($row['status']); ?>">
                        <?php echo ucfirst($row['status']); ?>
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
