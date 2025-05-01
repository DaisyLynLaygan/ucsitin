<?php
session_start(); // Start the session
include './connection.php';

// Ensure session variables are set
if (!isset($_SESSION['idno'])) {
    header("Location: login.php");
    exit;
}

// Retrieve session values safely
$firstname = $_SESSION['firstname'] ?? '';
$lastname = $_SESSION['lastname'] ?? '';
$profile_picture = $_SESSION['profile_picture'] ?? 'de.jpg';
$idno = $_SESSION['idno'];

// Get remaining sessions from the database
$sessionsResult = $conn->query("SELECT sessions FROM student WHERE idno = '$idno'");
$remainingSessions = 0;
if ($sessionsResult && $sessionsResult->num_rows > 0) {
    $remainingSessions = $sessionsResult->fetch_assoc()['sessions'];
}

// Handle profile picture upload
$upload_dir = "uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_picture"])) {
    if ($_FILES["profile_picture"]["error"] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES["profile_picture"]["type"];
        $file_name = basename($_FILES["profile_picture"]["name"]);
        $target_file = $upload_dir . $file_name;

        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $_SESSION['profile_picture'] = $target_file;
                $profile_picture = $target_file;
            }
        }
    }
}

// Handle reservation
$reservationSuccess = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitReserve'])) {
    $lab = $_POST['lab'] ?? '';
    $date = $_POST['date'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $reason = $_POST['reason'] ?? '';
    $pc_no = $_POST['pc'] ?? '';
    $status = 'pending';

    if ($lab && $date && $start_time && $reason && $pc_no) {
        $sql = "INSERT INTO reservations (user_id, lab, date, start_time, reason, pc_no, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssss", $idno, $lab, $date, $start_time, $reason, $pc_no, $status);

        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit(); // Prevent re-submission
        }
        $stmt->close();
    }
}

$reservationSuccess = isset($_GET['success']) && $_GET['success'] == '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sit-in Monitoring Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #0f172a;
            display: flex;
        }
        .sidebar {
            width: 220px;
            background-color: #6a0dad;
            color: white;
            padding: 20px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
        }
        .profile-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid white;
            object-fit: cover;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin-top: 20px;
        }
        .sidebar ul li {
            margin: 10px 0;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            padding: 10px;
            display: block;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .sidebar ul li a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .main-content {
            margin-left: 260px;
            padding: 40px;
            width: calc(100% - 260px);
            color: white;
        }
        h2 {
            color: #fafafa;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
        }
        input, select {
            width: 100%;
            padding: 10px 12px;
            font-size: 15px;
            border: none;
            border-radius: 8px;
            background-color: #a974d1;
            color: white;
            margin-bottom: 15px;
        }
        input[readonly] {
            background-color: #6a0dad;
        }
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right,  #a974d1,  #a974d1);
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            color: white;
        }
        table th, table td {
            padding: 8px;
            text-align: left;
        }
        table th {
            color:  #a974d1;
        }
        .form-section, .history-section {
            background: #1c1e26;
            padding: 30px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="profile-section">
            <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="profile-pic">
            <p><?php echo htmlspecialchars($firstname . " " . $lastname); ?></p>
        </div>
        <ul>
        <li><a href="dashboard.php">Home</a></li>
            <li><a href="profile.php">Edit Profile</a></li>
            <li><a href="announcements.php">View Announcements</a></li>
            <li><a href="SitinRules.php">Sit-in Rules</a></li>
            <li><a href="Labrules&Regulations.php">Lab Rules</a></li>
            <li><a href="Reservation.php">Reservation</a></li>
            <li><a href="SitinHistory.php">Sit-in History</a></li>
            <li><a href="LabResources.php">View Lab Resources</a></li>
            <li><a href="ViewSession.php">Session</a></li>
            <li><a href="Leaderboard.php">Leaderboard</a></li>
            <li><a href="LabSchedule.php">Lab Schedule</a></li>
            <li><a href="logout.php">Log Out</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h2>Lab Reservation</h2>
        <div style="display: flex; gap: 30px;">
            <div class="form-section" style="flex: 1;">
                <h3 style="color: white;">New Reservation</h3>
                <?php if ($reservationSuccess): ?>
                    <script>
                        alert("✅ Reservation submitted!");
                        window.scrollTo(0, 0);
                    </script>
                <?php endif; ?>
                <form method="POST" id="reservationForm">
                    <label>Student ID</label>
                    <input type="text" value="<?php echo htmlspecialchars($idno); ?>" readonly>

                    <label>Remaining Sessions</label>
                    <input type="text" value="<?php echo htmlspecialchars($remainingSessions); ?>" readonly>

                    <label>Purpose *</label>
                    <select name="reason" required>
                        <option value="">Select Purpose</option>
                        <option value="C Programming">C Programming</option>
                        <option value="Java Programming">Java Programming</option>
                        <option value="Python">Python</option>
                        <option value="PHP">PHP</option>
                        <option value="C#">C#</option>
                        <option value="Digilog">Digilog</option>
                        <option value="System Architecture">System Architecture</option>
                    </select>

                    <label>Laboratory Room *</label>
                    <select name="lab" id="lab-select" required>
                        <option value="">Select Lab</option>
                        <option value="524">Lab 524</option>
                        <option value="526">Lab 526</option>
                        <option value="528">Lab 528</option>
                        <option value="530">Lab 530</option>
                        <option value="542">Lab 542</option>
                        <option value="544">Lab 544</option>
                    </select>

                    <label>Select PC *</label>
                    <select name="pc" id="pc-select" required>
                        <option value="">Select a lab first</option>
                    </select>

                    <label>Date *</label>
                    <input type="date" name="date" required>

                    <label>Time In *</label>
                    <input type="time" name="start_time" required>

                    <button type="submit" name="submitReserve">Submit Reservation</button>
                </form>
            </div>
            <div class="history-section" style="flex: 1;">
                <h3 style="color: white;">Your Reservations</h3>
                <table>
                    <thead>
                        <tr>
                            <th>DATE</th>
                            <th>LAB</th>
                            <th>PC</th>
                            <th>TIME</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = $conn->query("SELECT * FROM reservations WHERE user_id = '$idno' ORDER BY id DESC LIMIT 5");
                        while ($row = $res->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['lab']); ?></td>
                            <td><?php echo htmlspecialchars($row['pc_no']); ?></td>
                            <td><?php echo htmlspecialchars($row['start_time']); ?></td>
                            <td><?php echo ucfirst($row['status']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
        const labSelect = document.getElementById('lab-select');
        const pcSelect = document.getElementById('pc-select');

        labSelect.addEventListener('change', function () {
            const labValue = labSelect.value;
            pcSelect.innerHTML = '<option value="">Loading PCs...</option>';

            if (!labValue) {
                pcSelect.innerHTML = '<option value="">Select a lab first</option>';
                return;
            }

            fetch(`fetch_pcs.php?lab=Lab%20${encodeURIComponent(labValue)}`)
                .then(res => res.json())
                .then(data => {
                    pcSelect.innerHTML = '<option value="">Select a PC</option>';
                    data.forEach(pc => {
                        const opt = document.createElement('option');
                        opt.value = pc.pc_no;
                        opt.textContent = pc.pc_no;
                        pcSelect.appendChild(opt);
                    });
                })
                .catch(() => {
                    pcSelect.innerHTML = '<option value="">Error loading PCs</option>';
                });
        });

        // Show success message if redirected
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get("success") === "1") {
            alert("✅ Reservation submitted!");
            history.replaceState(null, '', window.location.pathname); // Clean URL
        }
    });
    </script>
</body>
</html>

<?php $conn->close(); ?>
