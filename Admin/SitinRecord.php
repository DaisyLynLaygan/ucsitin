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

    <!-- ✅ DataTables CSS & JS -->
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
    <h2>Generate Reports</h2>

<label for="labFilter">Filter by Laboratory: </label>
<select id="labFilter">
    <option value="">All</option>
    <?php
        $labs = $conn->query("SELECT DISTINCT laboratory FROM sit_in");
        while ($lab = $labs->fetch_assoc()) {
            echo "<option value='{$lab['laboratory']}'>{$lab['laboratory']}</option>";
        }
    ?>
</select>

<table id="sitInTable" class="display nowrap" style="width:100%">
    <thead>
        <tr>
            <th>ID Number</th>
            <th>Name</th>
            <th>Purpose</th>
            <th>Laboratory</th>
            <th>Login</th>
            <th>Logout</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['idno']; ?></td>
                <td><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></td>
                <td><?php echo $row['purpose']; ?></td>
                <td><?php echo $row['laboratory']; ?></td>
                <td><?php echo date('h:i:sa', strtotime($row['sit_in_time'])); ?></td>
                <td><?php echo date('h:i:sa', strtotime($row['sit_out_time'])); ?></td>
                <td><?php echo date('Y-m-d', strtotime($row['sit_out_time'])); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
    </div> <!-- End of .container -->

<!-- ✅ DataTables Initialization Script -->
<script>
$(document).ready(function() {
    const table = $('#sitInTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['csv', 'excel', 'pdf', 'print'],
        responsive: true
    });

    $('#labFilter').on('change', function() {
        const selectedLab = $(this).val();
        table.column(3).search(selectedLab).draw();
    });
});
</script>
</body>
</html>

