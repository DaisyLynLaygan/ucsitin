<?php
include './connection.php'; // Database connection
include 'navbar.php';

// Fetch records where sit_out_time is NOT NULL (timed-out users)
$query = "SELECT s.idno, s.firstname, s.lastname, si.purpose, si.laboratory, si.sit_in_time, si.sit_out_time 
          FROM sit_in si
          JOIN student s ON si.idno = s.idno
          WHERE si.sit_out_time IS NOT NULL
          ORDER BY si.sit_out_time DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timed-Out Sit-in Records</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Timed-Out Sit-in Records</h2>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>ID No.</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Purpose</th>
                    <th>Laboratory</th>
                    <th>Sit-in Time</th>
                    <th>Timeout Time</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['idno']; ?></td>
                        <td><?php echo $row['firstname']; ?></td>
                        <td><?php echo $row['lastname']; ?></td>
                        <td><?php echo $row['purpose']; ?></td>
                        <td><?php echo $row['laboratory']; ?></td>
                        <td><?php echo $row['sit_in_time']; ?></td>
                        <td><?php echo $row['sit_out_time']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="no-data">No timed-out records available.</p>
        <?php endif; ?>
    </div>
</body>
</html>
