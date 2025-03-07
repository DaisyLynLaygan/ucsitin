<?php

include 'connection.php';
include 'navbar.php'; 

// Ensure the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: index.php");
    exit();
}

// Fetch all students from the database
$sql = "SELECT idno, lastname, firstname, middlename, course, year, email, username FROM student";
$result = $conn->query($sql);
?>
    <style>

        h2 {
            text-align: center;
            color: #6A0DAD;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #6A0DAD;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .container {
            max-width: 90%;
            margin: auto;
            overflow-x: auto;
        }
    </style>
        <h2>List of Registered Students</h2>
        <table>
            <thead>
                <tr>
                    <th>ID No</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Email</th>
                    <th>Username</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['idno']); ?></td>
                    <td><?php echo htmlspecialchars($row['lastname']); ?></td>
                    <td><?php echo htmlspecialchars($row['firstname']); ?></td>
                    <td><?php echo htmlspecialchars($row['middlename']); ?></td>
                    <td><?php echo htmlspecialchars($row['course']); ?></td>
                    <td><?php echo htmlspecialchars($row['year']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div> <!-- Closing main-content -->
</body>
</html>

<?php
$conn->close();
?>
