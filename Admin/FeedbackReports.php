<?php
include './connection.php'; // Database connection
include 'navbar.php';

// Fetch all feedback
$sql = "SELECT idno, laboratory, feedback, date FROM sit_in WHERE feedback IS NOT NULL ORDER BY date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Report</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Feedback Report</h1>
        
        <button onclick="printTable()">Print</button>
        <input type="text" id="search" placeholder="Filter">

        <table id="feedbackTable">
            <thead>
                <tr>
                    <th>Student ID Number</th>
                    <th>Laboratory</th>
                    <th>Date</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['idno']); ?></td>
                        <td><?php echo htmlspecialchars($row['laboratory']); ?></td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo htmlspecialchars($row['feedback']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Export buttons -->
        <button onclick="window.location.href='export_feedback.php?format=csv'">CSV</button>
        <button onclick="window.location.href='export_feedback.php?format=pdf'">PDF</button>
        <button onclick="window.location.href='export_feedback.php?format=doc'">DOC</button>
    </div>

    <script>
        function printTable() {
            window.print();
        }

        // Filter table
        $(document).ready(function(){
            $("#search").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#feedbackTable tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
</body>
</html>

//#6a0dad<?php
include './connection.php';

$result = $conn->query("SELECT idno, feedback, date FROM sit_in WHERE feedback IS NOT NULL ORDER BY date DESC");

echo "<h2>User Feedback Reports</h2>";
echo "<table border='1'>
<tr><th>ID No</th><th>Feedback</th><th>Date</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
    <td>{$row['idno']}</td>
    <td>{$row['feedback']}</td>
    <td>{$row['date']}</td>
    </tr>";
}
echo "</table>";

echo "<br><a href='export_feedback.php?format=csv'>Export as CSV</a> | ";
echo "<a href='export_feedback.php?format=pdf'>Export as PDF</a> | ";
echo "<a href='export_feedback.php?format=doc'>Export as DOC</a>";
?>
//