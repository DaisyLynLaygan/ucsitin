<?php
session_start();
include './connection.php'; // Database connection


// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch user data from the database
$sql = "SELECT idno, firstname, lastname, middlename, course, year, email, username, profile_picture 
        FROM student WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$userProfile = $result->fetch_assoc();

if (!$userProfile) {
    header("Location: login.php");
    exit();
}

$upload_dir = "uploads/";

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $middlename = $_POST['middlename'];
    $course = $_POST['course'];
    $year = $_POST['year'];
    $email = $_POST['email'];

    // Handle file upload if a new image is provided
    if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES["profile_picture"]["type"];
        $temp_file = $_FILES["profile_picture"]["tmp_name"];
        $file_name = preg_replace("/[^a-zA-Z0-9._-]/", "_", basename($_FILES["profile_picture"]["name"]));
        $target_file = $upload_dir . $file_name;

        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($temp_file, $target_file)) {
                $update_picture = $conn->prepare("UPDATE student SET profile_picture = ? WHERE username = ?");
                $update_picture->bind_param("ss", $target_file, $username);
                $update_picture->execute();
                $_SESSION['profile_picture'] = $target_file;
            }
        }
    }

    // Update user information in the database
    $update_profile = $conn->prepare("UPDATE student SET firstname = ?, lastname = ?, middlename = ?, course = ?, year = ?, email = ? WHERE username = ?");
    $update_profile->bind_param("sssssss", $firstname, $lastname, $middlename, $course, $year, $email, $username);
    $update_profile->execute();

    // Update session variables for real-time update across all pages
    $_SESSION['firstname'] = $firstname;
    $_SESSION['lastname'] = $lastname;
    $_SESSION['middlename'] = $middlename;
    $_SESSION['course'] = $course;
    $_SESSION['year'] = $year;
    $_SESSION['email'] = $email;
    
    $fetch_profile = $conn->prepare("SELECT idno, firstname, lastname, middlename, course, year, email, username, profile_picture FROM student WHERE username = ?");
    $fetch_profile->bind_param("s", $username);
    $fetch_profile->execute();
    $result = $fetch_profile->get_result();
    $userProfile = $result->fetch_assoc();
    
}
// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - CCS Sit-in Monitoring</title>
    <link rel="stylesheet" href="styles.css">
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(to right, #f5f7fa, #f0f4fc);
        margin: 0;
        display: flex;
    }

    .sidebar {
            width: 220px;
            background-color: #6a0dad;
            padding: 20px;
            height: 100vh;
            position: fixed;
            overflow-y: auto;
        }
        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid white;
            object-fit: cover;
        }
        .sidebar .profile-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
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
        }
        .sidebar ul li a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

    /* Main content */
    .main-content {
        margin-left: 180px;
        width: calc(100% - 180px);
        padding: 40px;
        background: linear-gradient(to right, #f9fafc, #f2f4f8);
        display: flex;
        justify-content: center;
    }

    /* Profile container */
    .profile-container {
        background: white;
        padding: 30px 40px;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        width: 100%;
        max-width: 900px;
    }
    .profile-container h2 {
        font-size: 22px;
        font-weight: 600;
        color: #6a0dad;
        margin-bottom: 30px;
        text-align: left;
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
    }

    /* Form layout */
    .profile-container form {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px 30px;
    }

    /* Label and Input */
    .profile-container label {
        font-weight: 500;
        margin-bottom: 6px;
        display: block;
        color: #444;
    }
    .profile-container input,
.profile-container select {
    width: 100%;
    padding: 12px 16px;
    font-size: 15px;
    border-radius: 8px;
    border: 1px solid #ccc;
    margin-top: 4px;
    margin-bottom: 18px;
    transition: all 0.3s ease;
    background-color: #f9f9f9;
}

.profile-container input:focus,
.profile-container select:focus {
    border-color: #6a0dad;
    box-shadow: 0 0 8px rgba(106, 13, 173, 0.2);
    background-color: #fff;
    outline: none;
}
    /* Button */
    button {
        grid-column: span 2;
        padding: 12px;
        background-color: #6a0dad;
        border: none;
        color: white;
        font-size: 16px;
        border-radius: 10px;
        cursor: pointer;
        transition: 0.3s;
    }
    button:hover {
        background-color: #5c0cc2;
    }

    /* Hidden file input */
    .hidden-input {
        display: none;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .profile-container form {
            grid-template-columns: 1fr;
        }
        .main-content {
            margin-left: 160px;
            padding: 20px;
        }
    }
 </style>
</head>
<body>
    <div class="sidebar">
        <div class="profile-section">
            <img src="<?php echo htmlspecialchars($userProfile['profile_picture'] ?? 'de.jpg'); ?>" alt="Profile Picture" class="profile-pic">
            <p><?php echo htmlspecialchars($userProfile['firstname'] . " " . $userProfile['lastname']); ?></p>
        </div>
        <ul>
        <li><a href="dashboard.php">Home</a></li>
            <li><a href="profile.php">Edit Profile</a></li>
            <li><a href="announcements.php">View Announcements</a></li>
            <li><a href="SitinRules.php">Sit-in Rules</a></li>
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
        <div class="profile-container">
            <h2>User Profile</h2>
            <form action="profile.php" method="post" enctype="multipart/form-data" style="max-width: 850px; margin: 0 auto;">
    <!-- Profile Header -->
    <div style="text-align: center; margin-bottom: 30px;">
        <label for="profile_picture" style="cursor: pointer;">
            <img src="<?php echo htmlspecialchars($userProfile['profile_picture'] ?? 'default.jpg'); ?>" 
                 alt="Profile Picture" 
                 style="width: 110px; height: 110px; object-fit: cover; border-radius: 50%; border: 4px solid #6a0dad;">
        </label>
        <input type="file" name="profile_picture" id="profile_picture" class="hidden-input" accept="image/*" style="display: none;">
        <h2 style="margin-top: 15px; font-size: 22px;"><?php echo htmlspecialchars($userProfile['firstname'] . " " . $userProfile['lastname']); ?></h2>
        <p style="color: #777;"><?php echo htmlspecialchars($userProfile['email']); ?></p>
    </div>

    <!-- Grid Layout -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">

        <!-- Left Column -->
        <div>
            <label for="idno">ID No:</label>
            <input type="text" name="idno" value="<?php echo htmlspecialchars($userProfile['idno']); ?>" readonly>

            <label for="firstname">First Name:</label>
            <input type="text" name="firstname" value="<?php echo htmlspecialchars($userProfile['firstname']); ?>" required>

            <label for="middlename">Middle Name:</label>
            <input type="text" name="middlename" value="<?php echo htmlspecialchars($userProfile['middlename']); ?>">

            <label for="email">Email Address:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($userProfile['email']); ?>" required>
        </div>

        <!-- Right Column -->
        <div>
            <label for="lastname">Last Name:</label>
            <input type="text" name="lastname" value="<?php echo htmlspecialchars($userProfile['lastname']); ?>" required>

            <label for="course">Course:</label>
            <select name="course" required>
                <option value="BSIT" <?php echo ($userProfile['course'] == 'BSIT') ? 'selected' : ''; ?>>BSIT</option>
                <option value="BSCS" <?php echo ($userProfile['course'] == 'BSCS') ? 'selected' : ''; ?>>BSCS</option>
                <option value="BEED" <?php echo ($userProfile['course'] == 'BEED') ? 'selected' : ''; ?>>BEED</option>
                <option value="BSED" <?php echo ($userProfile['course'] == 'BSED') ? 'selected' : ''; ?>>BSED</option>
                <option value="BSHM" <?php echo ($userProfile['course'] == 'BSHM') ? 'selected' : ''; ?>>BSHM</option>
                <option value="BSNS" <?php echo ($userProfile['course'] == 'BSNS') ? 'selected' : ''; ?>>BSNS</option>
                <option value="BSPYS" <?php echo ($userProfile['course'] == 'BSPYS') ? 'selected' : ''; ?>>BSPYS</option>
                <option value="BSATNG" <?php echo ($userProfile['course'] == 'BSATNG') ? 'selected' : ''; ?>>BSATNG</option>
            </select>

            <label for="year">Year Level:</label>
            <input type="number" name="year" value="<?php echo htmlspecialchars($userProfile['year']); ?>" required>

            <label for="username">Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($userProfile['username']); ?>" readonly>
        </div>
    </div>

    <!-- Submit -->
    <div style="margin-top: 30px; text-align: center;">
        <button type="submit" style="width: 200px; background-color: #6a0dad;">Update Profile</button>
    </div>
</form>

        </div>
    </div>
</body>
</html>
