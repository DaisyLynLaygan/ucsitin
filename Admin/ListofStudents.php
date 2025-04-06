<?php

include 'connection.php';
include 'navbar.php'; 

// Ensure the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: index.php");
    exit();
}

// Fetch all students from the database
$sql = "SELECT idno, lastname, firstname, middlename, course, year, email, username, session_no FROM student";
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

        <div class="filter-bar" style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0;">
            <div style="display: flex; gap: 10px; align-items: center;">
                <label for="filterCourse"><strong>Filter by Course:</strong></label>
                <select id="filterCourse">
                    <option value="">All</option>
                    <option value="BSCS">BSCS</option>
                    <option value="BSIT">BSIT</option>
                    <option value="BSEMC">BSEMC</option>
                    <!-- Add more courses as needed -->
                </select>

                <label for="filterYear"><strong>Filter by Year:</strong></label>
                <select id="filterYear">
                    <option value="">All</option>
                    <option value="1st Year">1st Year</option>
                    <option value="2nd Year">2nd Year</option>
                    <option value="3rd Year">3rd Year</option>
                    <option value="4th Year">4th Year</option>
                    <option value="5th Year">5th Year</option>
                </select>
            </div>

            <input type="text" id="searchInput" placeholder="Search..." style="padding: 5px; width: 250px;">
        </div>

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
                    <th>Session No.</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    // Function to convert year to ordinal meaning to pass year or number so that it will return based on the condition
                    // 1st year, 2nd year, 3rd year, 4th year, 5th year depends on the condition
                    // Inside function
                    function ordinalYear($year) {
                        switch ($year) {
                            case 1: return '1st Year';
                            case 2: return '2nd Year';
                            case 3: return '3rd Year';
                            case 4: return '4th Year';
                            case 5: return '5th Year';
                            default: return 'N/A';
                        }
                    }

                    while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['idno']); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst(strtolower($row['lastname']))); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst(strtolower($row['firstname']))); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst(strtolower($row['middlename']))); ?></td>
                    <td><?php echo htmlspecialchars(strtoupper($row['course'])); ?></td>
                    <td><?php echo htmlspecialchars(ordinalYear($row['year'])); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['session_no']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div> <!-- Closing main-content -->

        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('searchInput');
            const filterCourse = document.getElementById('filterCourse');
            const filterYear = document.getElementById('filterYear');
            const rows = document.querySelectorAll('tbody tr');

            function filterTable() {
                const searchValue = searchInput.value.toLowerCase();
                const courseValue = filterCourse.value;
                const yearValue = filterYear.value;

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    const course = row.children[4].textContent.trim();
                    const year = row.children[5].textContent.trim();

                    const matchSearch = text.includes(searchValue);
                    const matchCourse = !courseValue || course === courseValue;
                    const matchYear = !yearValue || year === yearValue;

                    if (matchSearch && matchCourse && matchYear) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('input', filterTable);
            filterCourse.addEventListener('change', filterTable);
            filterYear.addEventListener('change', filterTable);
        });
        </script>
</body>
</html>

<?php
$conn->close();
?>
