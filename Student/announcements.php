<?php
include 'connection.php';

// Fetch all announcements from the database
$result = $conn->query("SELECT * FROM announcements ORDER BY date_posted DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Announcements</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Announcements</h2>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="announcement">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
            <small>Posted on: <?php echo $row['date_posted']; ?></small>
        </div>
    <?php endwhile; ?>
</body>
</html>
