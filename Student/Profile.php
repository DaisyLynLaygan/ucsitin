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
    
    // Fetch the updated data from the database
    $stmt->execute();
    $result = $stmt->get_result();
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
        }
        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid white;
            cursor: pointer;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            width: 100%;
        }
        .sidebar ul li {
            padding: 15px;
            text-align: center;
            transition: background 0.3s;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
        }
        .sidebar ul li:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .main-content {
            margin-left: 270px;
            padding: 30px;
            width: calc(100% - 270px);
        }
        .profile-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: auto;
        }
        .profile-container h2 {
            text-align: center;
        }
        .profile-container input, .profile-container select, .profile-container button {
            width: 100%;
            margin-top: 10px;
            padding: 10px;
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
            <li><a href="profile.php">Profile</a></li>
            <li><a href="SitinRules.php">Sit-in Rules</a></li>
            <li><a href="Labrules&Regulations.php">Lab Rules & Regulations</a></li>
            <li><a href="announcements.php">Announcement</a></li>
            <li><a href="Reservation.php">Reservation</a></li>
            <li><a href="SitinHistory.php">Sit-in History</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="profile-container">
            <h2>User Profile</h2>
            <form action="profile.php" method="post" enctype="multipart/form-data">
                <label for="profile_picture">
                    <img src="<?php echo htmlspecialchars($userProfile['profile_picture'] ?? 'default.jpg'); ?>" alt="Profile Picture" class="profile-pic">
                </label>
                <input type="file" name="profile_picture" id="profile_picture" class="hidden-input" accept="image/*">
                
                <label for="idno">ID No:</label>
                <input type="text" name="idno" value="<?php echo htmlspecialchars($userProfile['idno']); ?>" readonly>

                <label for="firstname">First Name:</label>
                <input type="text" name="firstname" value="<?php echo htmlspecialchars($userProfile['firstname']); ?>" required>

                <label for="lastname">Last Name:</label>
                <input type="text" name="lastname" value="<?php echo htmlspecialchars($userProfile['lastname']); ?>" required>

                <label for="middlename">Middle Name:</label>
                <input type="text" name="middlename" value="<?php echo htmlspecialchars($userProfile['middlename']); ?>">

                <label for="course">Course:</label>
                <select name="course" id="course" required>
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

                <label for="email">Email Address:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($userProfile['email']); ?>" required>

                <label for="username">Username:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($userProfile['username']); ?>" readonly>

                <button type="submit">Update Profile</button>
            </form>
        </div>
    </div>
</body>
</html>
