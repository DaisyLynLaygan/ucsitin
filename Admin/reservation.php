<?php
include 'db_connection.php';

// Approve if action sent
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE reservations SET status = 'approved', message = 'Sit-in approved' WHERE id = $id");
    header("Location: reservation-approval.php");
    exit;
}

$reservations = $conn->query("SELECT r.*, u.firstname, u.lastname 
                              FROM reservations r 
                              JOIN users u ON r.user_id = u.id 
                              WHERE r.status = 'pending'");
?>

<h2>Pending Reservations</h2>
<table border="1" cellpadding="10">
    <tr>
        <th>Student</th>
        <th>Lab</th>
        <th>Date</th>
        <th>Time</th>
        <th>Language</th>
        <th>Purpose</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $reservations->fetch_assoc()): ?>
    <tr>
        <td><?= $row['firstname'] . ' ' . $row['lastname'] ?></td>
        <td><?= $row['lab'] ?></td>
        <td><?= $row['date'] ?></td>
        <td><?= $row['start_time'] ?> - <?= $row['end_time'] ?></td>
        <td><?= $row['language'] ?></td>
        <td><?= $row['reason'] ?></td>
        <td><a href="?approve=<?= $row['id'] ?>">✅ Approve</a></td>
    </tr>
    <?php endwhile; ?>
</table>
