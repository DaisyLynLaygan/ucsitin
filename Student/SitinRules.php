<?php
session_start();
include './connection.php'; // Database connection

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ensure the uploads directory exists
$upload_dir = "uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true); // Create the directory if it does not exist
}

// Fetch user profile from session or database
$userProfile = [
    'firstname' => $_SESSION['firstname'] ?? '',
    'lastname' => $_SESSION['lastname'] ?? '',
    'middlename' => $_SESSION['middlename'] ?? '',
    'profile_picture' => $_SESSION['profile_picture'] ?? 'de.jpg'
];

$remainingSessions = 30; // Example value, ideally retrieved from a database

// Handle profile picture upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] === 0) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = $_FILES["profile_picture"]["type"];
    $temp_file = $_FILES["profile_picture"]["tmp_name"];
    $file_name = preg_replace("/[^a-zA-Z0-9._-]/", "_", basename($_FILES["profile_picture"]["name"])); // Remove special characters
    $target_file = $upload_dir . $file_name;

    if (in_array($file_type, $allowed_types)) {
        if (move_uploaded_file($temp_file, $target_file)) {
            $_SESSION['profile_picture'] = $target_file;
            $userProfile['profile_picture'] = $target_file;
        } else {
            echo "<script>alert('Error moving file. Please check folder permissions.');</script>";
        }
    } else {
        echo "<script>alert('Invalid file format. Please upload JPG, PNG, or GIF.');</script>";
    }
}

// Handle profile updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['firstname'] = $_POST['firstname'] ?? $userProfile['firstname'];
    $_SESSION['lastname'] = $_POST['lastname'] ?? $userProfile['lastname'];
    $_SESSION['middlename'] = $_POST['middlename'] ?? $userProfile['middlename'];

    $userProfile['firstname'] = $_SESSION['firstname'];
    $userProfile['lastname'] = $_SESSION['lastname'];
    $userProfile['middlename'] = $_SESSION['middlename'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sit-in Monitoring Dashboard</title>
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            background-color: whitesmoke;
            margin: 0;
            overflow: hidden;
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
        .hidden-input {
            display: none;
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
            padding: 40px;
            width: calc(100% - 270px); /* Adjusted for sidebar width */
            display: flex;
            flex-direction: column;
            align-items: center; /* Centers content horizontally */
            justify-content: center; /* Centers content vertically */
            min-height: 100vh; /* Ensures it takes the full viewport height */
            margin-left: 270px; /* Pushes it to the right of the sidebar */
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
<!-- Rules and Regulations -->
<div class="card shadow-lg" style="max-width: 40rem;">
<div class="card-header text-white text-center" style="background-color:#CDABEB;">
<h5 class="mb-0">Sit-In Rules</h5>
</div>
<div class="card-body" style="max-height: 500px; overflow-y: auto;">
 <h5 class="text-center"><strong>University of Cebu</strong></h5>
            <p class="mb-2 text-center"><strong>COLLEGE OF INFORMATION & COMPUTER STUDIES</strong></p>
            <br>
            <p><strong>LABORATORY RULES AND REGULATIONS</strong></p>
            <p>To avoid embarrassment and maintain camaraderie with your friends and superiors at our laboratories, please observe the following:</p>
            <p>1. Maintain silence, proper decorum, and discipline inside the laboratory. Mobile phones, walkmans and other personal pieces of equipment must be switched off.</p>
            <p>2. Games are not allowed inside the lab. This includes computer-related games, card games and other games that may disturb the operation of the lab.</p>
            <p>3. Surfing the Internet is allowed only with the permission of the instructor. Downloading and installing of software are strictly prohibited.</p>
            <p>4. Getting access to other websites not related to the course (especially pornographic and illicit sites) is strictly prohibited.</p>
            <p>5. Deleting computer files and changing the set-up of the computer is a major offense.</p>
            <p>6. Observe computer time usage carefully. A fifteen-minute allowance is given for each use. Otherwise, the unit will be given to those who wish to "sit-in".</p>
            <p>7. Observe proper decorum while inside the laboratory.</p>
            <ul>
              <li>Do not get inside the lab unless the instructor is present.</li>
              <li>All bags, knapsacks, and the likes must be deposited at the counter.</li>
              <li>Follow the seating arrangement of your instructor.</li>
              <li>At the end of class, all software programs must be closed.</li>
              <li>Return all chairs to their proper places after using.</li>
            </ul>
            <p>8. Chewing gum, eating, drinking, smoking, and other forms of vandalism are prohibited inside the lab.</p>
            <p>9. Anyone causing a continual disturbance will be asked to leave the lab. Acts or gestures offensive to the members of the community, including public display of physical intimacy, are not tolerated.</p>
            <p>10. Persons exhibiting hostile or threatening behavior such as yelling, swearing, or disregarding requests made by lab personnel will be asked to leave the lab.</p>
            <p>11. For serious offense, the lab personnel may call the Civil Security Office (CSU) for assistance.</p>
            <p>12. Any technical problem or difficulty must be addressed to the laboratory supervisor, student assistant or instructor immediately.</p>
            <br>
            <p><strong>DISCIPLINARY ACTION</strong></p>
            <ul>
              <li>First Offense - The Head or the Dean or OIC recommends to the Guidance Center for a suspension from classes for each offender.</li>
              <li>Second and Subsequent Offenses - A recommendation for a heavier sanction will be endorsed to the Guidance Center.</li>
            </ul>
</div>
</div>
 
</div>
        
    </div>
</body>
</html>

