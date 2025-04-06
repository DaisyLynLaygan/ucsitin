<?php
include './connection.php'; // Database connection
include 'navbar.php';

// Function to detect foul words
function containsFoulWords($text) {
    $foulWords = ['badword1', 'badword2', 'stupid', 'idiot']; // Customize list
    foreach ($foulWords as $word) {
        if (stripos($text, $word) !== false) {
            return true;
        }
    }
    return false;
}

// Fetch feedbacks with student info
$sql = "SELECT s.idno, s.firstname, s.course, si.laboratory, si.purpose, si.sit_in_time, si.sit_out_time, 
               f.feedback_date, f.feedback 
        FROM sit_in si
        JOIN student s ON s.idno = si.idno
        JOIN feedback f ON f.sit_in_id = si.id
        WHERE si.sit_out_time IS NOT NULL
        AND f.feedback IS NOT NULL
        ORDER BY f.feedback_date DESC";

$result = $conn->query($sql);
if (!$result) {
    die("<p style='color:red;'>Error fetching feedbacks: " . $conn->error . "</p>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Report</title>
    <link rel="stylesheet" href="styles.css">

    <!-- DataTables CSS & JS for export -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
</head>
<body>
<div class="container">
    <h1>Feedback Report</h1>

    <button onclick="printTable()">Print</button>
    <input type="text" id="search" placeholder="Filter">

    <table id="feedbackTable">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>First Name</th>
                <th>Course</th>
                <th>Laboratory</th>
                <th>Login</th>
                <th>Logout</th>
                <th>Date</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()) : 
            $isFoul = containsFoulWords($row['feedback']);
        ?>
            <tr <?php if ($isFoul) echo 'style="background-color: #ffe6e6;"'; ?>>
                <td><?php echo htmlspecialchars($row['idno']); ?></td>
                <td><?php echo htmlspecialchars($row['firstname']); ?></td>
                <td><?php echo htmlspecialchars($row['course']); ?></td>
                <td><?php echo htmlspecialchars($row['laboratory']); ?></td>
                <td><?php echo htmlspecialchars($row['sit_in_time']); ?></td>
                <td><?php echo htmlspecialchars($row['sit_out_time']); ?></td>
                <td><?php echo htmlspecialchars($row['feedback_date']); ?></td>
                <td>
                    <button onclick="viewMessage('<?php echo addslashes($row['feedback']); ?>')">View</button>
                    <?php if ($isFoul): ?>
                        <span title="Contains foul language">⚠️</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Export buttons -->
    <button onclick="window.location.href='export_feedback.php?format=csv'">CSV</button>
    <button onclick="window.location.href='export_feedback.php?format=pdf'">PDF</button>
    <button onclick="window.location.href='export_feedback.php?format=doc'">DOC</button>
</div>

<!-- Feedback Modal -->
<div id="feedbackModal" style="display:none; position:fixed; top:20%; left:30%; background:white; padding:20px; border:1px solid #ccc; z-index:1000;">
    <h3>Feedback Message</h3>
    <p id="feedbackContent"></p>
    <button onclick="document.getElementById('feedbackModal').style.display='none';">Close</button>
</div>

<script>
function viewMessage(message) {
    document.getElementById('feedbackContent').innerText = message;
    document.getElementById('feedbackModal').style.display = 'block';
}

$(document).ready(function () {
    $('#feedbackTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['csv', 'excel', 'pdf', 'print']
    });

    $('#search').on("keyup", function () {
        $('#feedbackTable').DataTable().search($(this).val()).draw();
    });
});

function printTable() {
    window.print();
}
</script>
</body>
</html>
