<?php
include './connection.php'; // Include database connection
include 'navbar.php'; // Include navigation bar
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Reports</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <div class="container">
        <h2>Generate Reports</h2>
        <label for="date">Date: </label>
        <input type="date" id="dateFilter">
        <label for="lab">Laboratory: </label>
        <select id="labFilter">
            <option value="">All</option>
            <option value="524">524</option>
            <option value="526">526</option>
            <option value="528">528</option>
            <option value="517">517</option>
            <option value="544">544</option>
            <option value="530">530</option>
        </select>
        <button id="searchBtn">Search</button>
        <button id="resetBtn">Reset</button>
        
        <table id="reportTable" class="display">
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
                <?php
                $sql = "SELECT * FROM sitin_reports";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['id_number']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['purpose']}</td>
                        <td>{$row['lab']}</td>
                        <td>{$row['login_time']}</td>
                        <td>{$row['logout_time']}</td>
                        <td>{$row['date']}</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            var table = $('#reportTable').DataTable();

            $('#searchBtn').click(function() {
                var date = $('#dateFilter').val();
                var lab = $('#labFilter').val();
                
                table.column(6).search(date).draw();
                table.column(3).search(lab).draw();
            });

            $('#resetBtn').click(function() {
                $('#dateFilter').val('');
                $('#labFilter').val('');
                table.search('').columns().search('').draw();
            });
        });
    </script>
</body>
</html>
