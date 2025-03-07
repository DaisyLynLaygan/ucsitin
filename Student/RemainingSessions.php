<?php
session_start();
include 'db_connect.php'; // Include your database connection file

// Fetch remaining sessions from the database
$user_id = $_SESSION['user_id']; // Assume user session is set
$query = "SELECT remaining_sessions FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$remainingSessions = $row['remaining_sessions'];

// Handle session decrement (if a user uses a session)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['use_session'])) {
    if ($remainingSessions > 0) {
        $remainingSessions--;
        $updateQuery = "UPDATE users SET remaining_sessions = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ii", $remainingSessions, $user_id);
        $stmt->execute();
    }
    header("Location: remainingsessions.php"); // Refresh page after update
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remaining Sessions</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: whitesmoke;
            text-align: center;
            padding: 50px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: inline-block;
            text-align: center;
        }
        button {
            background-color: purple;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Remaining Sessions</h2>
        <p><strong><?php echo $remainingSessions; ?></strong></p>
        <form method="POST">
            <button type="submit" name="use_session">Use a Session</button>
        </form>
    </div>
</body>
</html>