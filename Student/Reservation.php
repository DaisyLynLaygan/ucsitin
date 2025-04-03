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

// Define userProfile array
$userProfile = [
    'firstname' => $firstname,
    'lastname' => $lastname,
    'profile_picture' => $profile_picture
];

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

// Database connection
$conn = new mysqli('localhost', 'root', '', 'sitin');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle reservation
$reservationSuccess = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitReserve'])) {
    $lab = $_POST['lab'] ?? '';
    $date = $_POST['date'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
    $reason = $_POST['reason'] ?? '';
    $language = $_POST['language'] ?? '';

    if ($lab && $date && $start_time && $end_time && $reason && $language) {
        $sql = "INSERT INTO reservations (user_id, lab, date, start_time, end_time, reason, language) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssss", $idno, $lab, $date, $start_time, $end_time, $reason, $language);

        if ($stmt->execute()) {
            $reservationSuccess = true;
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sit-in Monitoring Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <!-- FullCalendar CSS and JS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <style>
      body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background-color: #f4f2fa;
    display: flex;
}

/* Sidebar */
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

/* Main Layout */
.main-content {
    margin-left: 300px;
    margin-right: 50px;
    padding: 40px;
    flex-grow: 1;
    width: 100%;
}

h2 {
    color: #5a2d82;
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 30px;
}

/* Grid layout for form and calendar */
.reservation-layout {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 50px;
}

/* Form Card */
.form-card {
    background: white;
    padding: 50px;
    border-radius: 15px;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
}

label {
    font-weight: 600;
    color: #5a2d82;
    margin-bottom: 5px;
}

input, select {
    width: 90%;
    padding: 10px 12px;
    font-size: 15px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background-color: #fafafa;
    margin-bottom: 10px;
}

input:focus, select:focus {
    outline: none;
    border-color: #a974d1;
    background-color: #fff;
    box-shadow: 0 0 5px rgba(106, 13, 173, 0.3);
}
input:focus,
select:focus {
    outline: none;
    border-color: #a974d1;
    background-color: #fff;
}
/* Button */
button {
    width: 100%;
    padding: 12px;
    background-color: #a74ac7;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 10px;
}

button:hover {
    background-color: #883fbd;
}

.success-message {
    color: green;
    font-weight: bold;
    margin-bottom: 15px;
}
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="profile-section">
      <!-- Replace $userProfile with the correct variables -->
<img src="<?php echo htmlspecialchars($profile_picture); ?>" 
     alt="Profile Picture" class="profile-pic" id="display-pic">

<p><?php echo htmlspecialchars($firstname . " " . $lastname); ?></p>
        </div>
        <ul>
        <li><a href="dashboard.php">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="SitinRules.php">Sit-in Rules</a></li>
            <li><a href="Labrules&Regulations.php">Lab Rules</a></li>
            <li><a href="announcements.php">Announcement</a></li>
            <li><a href="Reservation.php">Reservation</a></li>
            <li><a href="SitinHistory.php">History</a></li>
            <li><a href="ViewSession.php">Session</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
<!-- Reservation Form -->
<div class="main-content">
    <h2 style="text-align: center; color: #5a2d82; font-weight: bold; margin-bottom: 30px;">Lab Reservation</h2>

    <!-- Reservation Form Container -->
    <div style="max-width: 950px; margin: 0 auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.08);">
        <?php if ($reservationSuccess): ?>
            <p style="color: green; text-align: center; font-weight: bold;">Reservation successful!</p>
        <?php endif; ?>

        <form method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label for="lab">Choose a Lab:</label>
                    <select name="lab" required>
                        <option value="">Select a Lab</option>
                        <option value="524">Lab 524</option>
                        <option value="544">Lab 544</option>
                        <option value="530">Lab 530</option>
                        <option value="526">Lab 526</option>
                        <option value="542">Lab 542</option>
                        <option value="528">Lab 528</option>
                        <option value="517">Lab 517</option>
                    </select>
                </div>

                <div>
                    <label for="date">Date:</label>
                    <input type="date" name="date" required>
                </div>

                <div>
                    <label for="start_time">Start Time:</label>
                    <input type="time" name="start_time" required>
                </div>

                <div>
                    <label for="end_time">End Time:</label>
                    <input type="time" name="end_time" required>
                </div>

                <div>
                    <label for="language">Programming Language:</label>
                    <select name="language" required>
                        <option value="">Select Language</option>
                        <option value="JavaScript">JavaScript</option>
                        <option value="Java">Java</option>
                        <option value="C#">C#</option>
                        <option value="Python">Python</option>
                        <option value="PHP">PHP</option>
                    </select>
                </div>

                <div>
                    <label for="reason">Purpose:</label>
                    <input type="text" name="reason" required>
                </div>
            </div>

            <!-- Submit Button -->
            <div style="margin-top: 30px; text-align: center;">
                <button type="submit" name="submitReserve" style="padding: 12px 25px; font-size: 16px; background-color: #6a0dad; color: white; border: none; border-radius: 8px; cursor: pointer;">
                    Reserve
                </button>
            </div>
        </form>
    </div>
</body>
</html>

