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
<<<<<<< HEAD

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

        <label for="purposeFilter" style="margin-left: 20px;">Filter by Purpose: </label>
        <select id="purposeFilter">
            <option value="">All</option>
            <?php
                $purposes = $conn->query("SELECT DISTINCT purpose FROM sit_in");
                while ($purpose = $purposes->fetch_assoc()) {
                    echo "<option value='{$purpose['purpose']}'>{$purpose['purpose']}</option>";
                }
            ?>
        </select>
        
        <label for="labFilter" style="margin: 2px">Filter by Laboratory Room: </label>
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
                    <th>Student Name</th>
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
    $(document).ready(function () {
        const table = $('#sitInTable').DataTable({
            dom: 'Bfrtip',
            buttons: ['csv', 'excel', 'pdf', 'print'],
            responsive: true
        });

        // Variables to store currently selected filter values
        let selectedPurpose = '';
        let selectedLab = '';

        // Function to update the Purpose filter options based on the current filtered rows
        function updatePurposeOptions() {
            const purposes = new Set(); // Create a Set to hold unique purposes
            // Loop through all the rows currently visible (filtered)
            table.rows({ filter: 'applied' }).every(function () {
                purposes.add(this.data()[2]); // Add purpose from the current row (column index 2)
            });
            // Update the purpose dropdown with new options
            $('#purposeFilter').empty().append('<option value="">All</option>');
            purposes.forEach(purpose => {
                $('#purposeFilter').append(`<option value="${purpose}">${purpose}</option>`);
            });
            $('#purposeFilter').val(selectedPurpose); // Set the previously selected value
        }

        // Function to update the Laboratory filter options based on the current filtered rows
        function updateLabOptions() {
            const labs = new Set(); // Create a Set to hold unique laboratories
            // Loop through all the rows currently visible (filtered)
            table.rows({ filter: 'applied' }).every(function () {
                labs.add(this.data()[3]); // Add laboratory from the current row (column index 3)
            });
            // Update the lab dropdown with new options
            $('#labFilter').empty().append('<option value="">All</option>');
            labs.forEach(lab => {
                $('#labFilter').append(`<option value="${lab}">${lab}</option>`);
            });
            $('#labFilter').val(selectedLab); // Set the previously selected value
        }

        // Function to reset both filter options to include all available values from the entire dataset
        function resetAllOptions() {
            const allPurposes = new Set(); // Create a Set for all purposes in the dataset
            const allLabs = new Set(); // Create a Set for all laboratories in the dataset
            // Loop through all the rows in the table (not just filtered rows)
            table.rows().every(function () {
                const data = this.data();
                allPurposes.add(data[2]); // Add purpose (column index 2)
                allLabs.add(data[3]); // Add laboratory (column index 3)
            });

            // Reset the Purpose filter dropdown with all unique purposes
            $('#purposeFilter').empty().append('<option value="">All</option>');
            allPurposes.forEach(p => {
                $('#purposeFilter').append(`<option value="${p}">${p}</option>`);
            });

            // Reset the Laboratory filter dropdown with all unique laboratories
            $('#labFilter').empty().append('<option value="">All</option>');
            allLabs.forEach(l => {
                $('#labFilter').append(`<option value="${l}">${l}</option>`);
            });
        }

        // When the laboratory filter is changed:
        $('#labFilter').on('change', function () {
            selectedLab = $(this).val(); // Get the selected value of the lab filter
            table.column(3).search(selectedLab).draw(); // Filter table by laboratory (column 3)

            // If both filters are "All", reset both options
            if (selectedLab === '' && selectedPurpose === '') {
                resetAllOptions();
            } else {
                updatePurposeOptions(); // Update the purpose filter based on the selected laboratory
            }
        });

        // When the purpose filter is changed:
        $('#purposeFilter').on('change', function () {
            selectedPurpose = $(this).val(); // Get the selected value of the purpose filter
            table.column(2).search(selectedPurpose).draw(); // Filter table by purpose (column 2)

            // If both filters are "All", reset both options
            if (selectedLab === '' && selectedPurpose === '') {
                resetAllOptions();
            } else {
                updateLabOptions(); // Update the laboratory filter based on the selected purpose
            }
        });

        // Meaning example if the first selected on filter is Purpose then the selection on Laboratory will change and will based on the filtered data output
        // But if vice versa then the other one selection will be based on the filtered data output data
    });
    </script>
</body>
</html>

=======
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
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
