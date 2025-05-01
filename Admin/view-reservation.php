<?php
session_start();
include 'connection.php';
include 'navbar.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Fetch pending reservations
$sql = "SELECT r.*, s.firstname, s.lastname, s.idNo, s.sessions
        FROM reservations r
        JOIN student s ON r.user_id = s.idNo
        WHERE r.status = 'pending'
        ORDER BY r.date DESC";
$reservations = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Reservations</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
            display: flex;
        }

        .main-content {
            margin-left: 220px;
            padding: 40px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            font-size: 24px;
            color: #6a0dad;
            margin-bottom: 25px;
            text-align: left;
            margin-left: 90px;
            width: 100%;
        }

        .table-container {
            width: 90%;
            max-width: 1200px;
            background-color: #dec3f9;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
        }

        th {
            background-color: #6a0dad;
            color: #ffffff;
            font-weight: 600;
            border-bottom: 1px solid #4b0082;
        }

        td {
            color: #e2e8f0;
            border-bottom: 1px solid #6a5acd;
        }

        .btn {
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .btn-approve {
            background-color: #16a34a;
            color: white;
            margin-right: 8px;
        }

        .btn-disapprove {
            background-color: #dc2626;
            color: white;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <h2>Manage Reservations</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>ID</th>
                        <th>Purpose</th>
                        <th>Lab</th>
                        <th>PC</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Sessions Left</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $reservations->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?></td>
                        <td><?= htmlspecialchars($row['idNo']) ?></td>
                        <td><?= htmlspecialchars($row['reason']) ?></td>
                        <td><?= htmlspecialchars($row['lab']) ?></td>
                        <td><?= htmlspecialchars($row['language']) ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['start_time']) ?></td>
                        <td><?= htmlspecialchars($row['sessions']) ?></td>
                        <td>
                            <form method="POST" action="approve_reservation.php" style="display:inline;">
                                <input type="hidden" name="reservation_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn btn-approve">✔ Approve</button>
                            </form>
                            <form method="POST" action="approve_reservation.php" style="display:inline;">
                                <input type="hidden" name="reservation_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="action" value="disapprove">
                                <button type="submit" class="btn btn-disapprove">✖ Disapprove</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
