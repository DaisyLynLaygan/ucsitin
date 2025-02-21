<?php
session_start();
require 'db_connection.php'; // Include the database connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $reservation_date = $_POST['reservation_date'];
    $reservation_time = $_POST['reservation_time'];
    $lab = $_POST['lab'];
    
    $stmt = $conn->prepare("INSERT INTO reservations (user_id, reservation_date, reservation_time, lab) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $reservation_date, $reservation_time, $lab);
    
    if ($stmt->execute()) {
        $success_message = "Reservation successfully submitted!";
    } else {
        $error_message = "Error submitting reservation.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Reserve a Sit-in Session</h2>
        <?php if (isset($success_message)) echo "<p class='success'>$success_message</p>"; ?>
        <?php if (isset($error_message)) echo "<p class='error'>$error_message</p>"; ?>
        <form method="POST">
            <label for="reservation_date">Date:</label>
            <input type="date" name="reservation_date" required>
            
            <label for="reservation_time">Time:</label>
            <input type="time" name="reservation_time" required>
            
            <label for="lab">Select Lab:</label>
            <select name="lab" required>
                <option value="Lab 1">Lab 1</option>
                <option value="Lab 2">Lab 2</option>
                <option value="Lab 3">Lab 3</option>
            </select>
            
            <button type="submit">Submit Reservation</button>
        </form>
    </div>
</body>
</html>
