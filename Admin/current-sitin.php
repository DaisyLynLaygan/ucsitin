<?php
include './connection.php'; // Database connection
include 'navbar.php';

// Handle timeout
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $conn->real_escape_string($_POST['id']);
    
    // Update sit_out_time with the current timestamp
    $query = "UPDATE sit_in SET sit_out_time = NOW() WHERE id = '$id'";
    
    if ($conn->query($query) === TRUE) {
        header("Location: current-sitin.php"); // Redirect back to the same page
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Fetch only active sit-in records (exclude timed-out ones)
$query = "SELECT s.idno, s.firstname, s.lastname, si.purpose, si.laboratory, si.sit_in_time, si.id 
          FROM sit_in si
          JOIN student s ON si.idno = s.idno
          WHERE si.sit_out_time IS NULL
          ORDER BY si.sit_in_time DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Sit-in Records</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
        }
        .container {
            width: 80%;
            margin: 40px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: purple;
            color: white;
        }
        .no-data {
            color: red;
            font-weight: bold;
            margin-top: 20px;
        }
        .timeout-btn {
            background-color: red;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .timeout-btn:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Current Sit-in Records</h2>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>ID No.</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Purpose</th>
                    <th>Laboratory</th>
                    <th>Sit-in Time</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['idno']; ?></td>
                        <td><?php echo $row['firstname']; ?></td>
                        <td><?php echo $row['lastname']; ?></td>
                        <td><?php echo $row['purpose']; ?></td>
                        <td><?php echo $row['laboratory']; ?></td>
                        <td><?php echo $row['sit_in_time']; ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="timeout-btn">Timeout</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="no-data">No sit-in records available.</p>
        <?php endif; ?>
    </div>
</body>
</html>
