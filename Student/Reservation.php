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
<<<<<<< HEAD
$idno = $_SESSION['idno'];

// Get remaining sessions from the database
$sessionsResult = $conn->query("SELECT sessions FROM student WHERE idNo = '$idno'");
$remainingSessions = 0;
if ($sessionsResult && $sessionsResult->num_rows > 0) {
    $remainingSessions = $sessionsResult->fetch_assoc()['sessions'];
}
=======

// Define userProfile array
$userProfile = [
    'firstname' => $firstname,
    'lastname' => $lastname,
    'profile_picture' => $profile_picture
];
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1

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

<<<<<<< HEAD
=======
// Database connection
$conn = new mysqli('localhost', 'root', '', 'sitin');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
// Handle reservation
$reservationSuccess = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitReserve'])) {
    $lab = $_POST['lab'] ?? '';
    $date = $_POST['date'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
<<<<<<< HEAD
    $end_time = ''; // Not used in form
    $reason = $_POST['reason'] ?? '';
    $language = ''; // Not used in form
    $status = 'pending';

    if ($lab && $date && $start_time && $reason) {
        $sql = "INSERT INTO reservations (user_id, lab, date, start_time, end_time, reason, language, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssss", $idno, $lab, $date, $start_time, $end_time, $reason, $language, $status);
=======
    $end_time = $_POST['end_time'] ?? '';
    $reason = $_POST['reason'] ?? '';
    $language = $_POST['language'] ?? '';

    if ($lab && $date && $start_time && $end_time && $reason && $language) {
        $sql = "INSERT INTO reservations (user_id, lab, date, start_time, end_time, reason, language) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssss", $idno, $lab, $date, $start_time, $end_time, $reason, $language);
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1

        if ($stmt->execute()) {
            $reservationSuccess = true;
        }
        $stmt->close();
    }
}
<<<<<<< HEAD
=======
$conn->close();
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
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
<<<<<<< HEAD
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
=======
            display: flex;
            font-family: Arial, sans-serif;
            background-color: whitesmoke;
            margin: 0;
        }
        .sidebar {
                width: 250px;
                background-color: purple;
                color: white;
                height: 100vh;
                padding: 20px;
                position: fixed; 
                top: 0;
                left: 0;
                overflow-y: auto; 
            }

        .profile-section {
            text-align: center;
            margin-bottom: 20px;
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
        }
        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid white;
<<<<<<< HEAD
            object-fit: cover;
=======
            cursor: pointer;
        }
        .hidden-input {
            display: none;
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
<<<<<<< HEAD
            margin-top: 20px;
        }
        .sidebar ul li {
            margin: 10px 0;
=======
            width: 100%;
        }
        .sidebar ul li {
            padding: 15px;
            text-align: center;
            transition: background 0.3s;
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
<<<<<<< HEAD
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
=======
            display: block;
        }
        .sidebar ul li:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .main-content {
            margin-left: 270px;
            padding: 40px;
            width: calc(100% - 270px);
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            width: 100%;
            max-width: 900px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            .main-content {
                margin-left: 220px;
                width: calc(100% - 220px);
            }
            .dashboard-cards {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
        }
         /* Global styles */
         body {
            font-family: Arial, sans-serif;
            background-color: #f7f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        h2 {
            text-align: center;
            color: #6d597a;
        }
        label {
            display: block;
            margin-top: 10px;
            color: #5c5470;
            font-weight: bold;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #d3c0d2;
            border-radius: 5px;
            font-size: 14px;
        }
        button {
            background-color: #b5838d;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 15px;
        }
        button:hover {
            background-color: #6d597a;
        }
        .success-message {
            text-align: center;
            color: green;
            font-weight: bold;
            margin-bottom: 10px;
        }
        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="profile-section">
<<<<<<< HEAD
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
                    <p style="color: #22c55e; font-weight: bold;">✅ Reservation submitted!</p>
                <?php endif; ?>
                <form method="POST">
                    <label>Student ID</label>
                    <input type="text" value="<?php echo $idno; ?>" readonly>
                    <label>Name</label>
                    <input type="text" value="<?php echo $firstname . ' ' . $lastname; ?>" readonly>
                    <label>Remaining Sessions</label>
                    <input type="text" value="<?php echo $remainingSessions; ?>" readonly>
                    <label>Purpose *</label>
                    <select name="reason" required>
                        <option value="">Select Purpose</option>
                        <option value="C Programming">C Programming</option>
                        <option value="Java Programming">Java Programming</option>
                        <option value="Python">Python</option>
                        <option value="Python">PHP</option>
                        <option value="Python">C#</option>
                        <option value="Python">Digilog</option>
                        <option value="Python">System Architecture</option>
                    </select>
                    <label>Laboratory Room *</label>
                    <select name="lab" required>
                        <option value="">Select Lab</option>
                        <option value="Lab 517">Lab 524</option>
                        <option value="Lab 526">Lab 526</option>
                        <option value="Lab 526">Lab 528</option> 
                        <option value="Lab 526">Lab 530</option>
                        <option value="Lab 526">Lab 542</option>
                        <option value="Lab 526">Lab 544</option>
                    </select>
                    <label>Date *</label>
                    <input type="date" name="date" required>
                    <label>Time In *</label>
                    <input type="time" name="start_time" required>
                    <button type="submit" name="submitReserve">🚀 Submit Reservation</button>
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
                            <td>PC <?php echo rand(1, 10); ?></td>
                            <td><?php echo htmlspecialchars($row['start_time']); ?></td>
                            <td><?php echo ucfirst($row['status']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
=======
      <!-- Replace $userProfile with the correct variables -->
<img src="<?php echo htmlspecialchars($profile_picture); ?>" 
     alt="Profile Picture" class="profile-pic" id="display-pic">

<p><?php echo htmlspecialchars($firstname . " " . $lastname); ?></p>
        </div>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="SitinRules.php">Sit-in Rules</a></li>
            <li><a href="Labrules&Regulations.php">Lab Rules & Regulations</a></li>
            <li><a href="announcements.php">Announcement</a></li>
            <li><a href="Reservation.php">Reservation</a></li>
            <li><a href="SitinHistory.php">Sit-in History</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="container">
        <h2>Lab Reservation</h2>
        <?php if ($reservationSuccess): ?>
            <p style="color: green;">Reservation successful!</p>
        <?php endif; ?>
        <form method="POST">
            <label for="lab">Choose a Lab:</label>
            <select name="lab" required>
                <option value="524">Lab 524</option>
                <option value="544">Lab 544</option>
                <option value="530">Lab 530</option>
                <option value="526">Lab 526</option>
                <option value="542">Lab 542</option>
                <option value="528">Lab 528</option>
                <option value="517">Lab 517</option>
            </select>
            <label for="date">Date:</label>
            <input type="date" name="date" required>
            <label for="start_time">Start Time:</label>
            <input type="time" name="start_time" required>
            <label for="end_time">End Time:</label>
            <input type="time" name="end_time" required>
            <label for="language">Programming Language:</label>
            <select name="language" required>
                <option value="JavaScript">JavaScript</option>
                <option value="Java">Java</option>
                <option value="C#">C#</option>
                <option value="Python">Python</option>
                <option value="PHP">PHP</option>
            </select>
            <label for="reason">Purpose:</label>
            <input type="text" name="reason" required>
            <button type="submit" name="submitReserve">Reserve</button>
        </form>
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
    </div>
</body>
</html>

<<<<<<< HEAD
<?php $conn->close(); ?>
=======
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
