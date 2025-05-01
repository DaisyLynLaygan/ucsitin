<?php
session_start();
<<<<<<< HEAD
if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include 'connection.php'; 
?>

=======
include './connection.php'; // Database connection

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sit-in Monitoring Dashboard</title>
    <link rel="stylesheet" href="styles.css">
<<<<<<< HEAD
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
       body {
=======
    <style>
        body {
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
            display: flex;
            font-family: Arial, sans-serif;
            background-color: whitesmoke;
            margin: 0;
        }
        .sidebar {
<<<<<<< HEAD
            width: 200px;
            background-color: #6a0dad;
            color: white;
            height: 100vh;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
        }
          .sidebar ul li a i {
         margin-right: 8px;
             }
=======
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
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1

        .profile-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid white;
<<<<<<< HEAD
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
=======
            width: 100%;
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
        }
        .sidebar ul li {
            padding: 15px;
            text-align: center;
<<<<<<< HEAD
=======
            transition: background 0.3s;
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
        }
        .sidebar ul li:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
<<<<<<< HEAD
        .sidebar_logout:hover {
            cursor: pointer;
            background-color: red;
            color: white; 
            border-radius: 5px; 
        }
        .main-content {
            margin-left: 220px;
            padding: 40px;
            width: calc(100% - 220px);
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin: auto;
=======
        .main-content {
            margin-left: 270px;
            padding: 40px;
            width: calc(100% - 270px);
            display: flex;
            flex-direction: column;
            align-items: center;
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
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
<<<<<<< HEAD
        @media (max-width: 768px) 
=======
        @media (max-width: 768px) {
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
            .sidebar {
                width: 200px;
            }
            .main-content {
                margin-left: 220px;
                width: calc(100% - 220px);
            }
<<<<<<< HEAD

        /* Confirmation Modal Styles */      
        #confirmationModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            width: 300px;
        }

        .modal-content button {
            margin: 10px;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .modal-content button.confirm {
            background-color: red;
            color: white;
        }

        .modal-content button.cancel {
            background-color: #ccc;
        }

        /* Smooth Fade-in for Modal */
        #confirmationModal.show {
            display: flex;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
=======
            .dashboard-cards {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
            }
        }
    </style>
</head>
<body>
<<<<<<< HEAD
<div class="sidebar">
=======
    <div class="sidebar">
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
        <div class="profile-section">
            <img src="<?php echo $userProfile['profile_picture'] != '' ? htmlspecialchars($userProfile['profile_picture']) : 'de.jpg'; ?>" alt="Profile Picture" class="profile-pic" id="display-pic">
            <p><?php echo htmlspecialchars($userProfile['firstname'] . " " . $userProfile['lastname']); ?></p>
        </div>
        <ul>
<<<<<<< HEAD
        <li><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
        <li><a href="SitinRules.php"><i class="fas fa-book"></i> Sit-in Rules</a></li>
        <li><a href="Labrules&Regulations.php"><i class="fas fa-chalkboard-teacher"></i> Lab Rules & Regulations</a></li>
        <li><a href="announcements.php"><i class="fas fa-bullhorn"></i> Announcement</a></li>
        <li><a href="Reservation.php"><i class="fas fa-calendar-check"></i> Reservation</a></li>
        <li><a href="SitinHistory.php"><i class="fas fa-history"></i> Sit-in History</a></li>
        <li class="sidebar_logout"><a href="javascript:void(0);" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
     </ul>
    </div>
    <div class="main-content">
    <div id="confirmationModal">
            <div class="modal-content">
                <h3>Are you sure you want to log out?</h3>
                <button class="confirm" id="confirmLogout">Yes</button>
                <button class="cancel" id="cancelLogout">No</button>
            </div>
        </div>

    <script>
        // Get the modal and buttons
        const logoutBtn = document.getElementById('logoutBtn');
        const confirmationModal = document.getElementById('confirmationModal');
        const confirmLogout = document.getElementById('confirmLogout');
        const cancelLogout = document.getElementById('cancelLogout');

        // Show the modal when the logout button is clicked
        logoutBtn.addEventListener('click', function (e) {
            e.preventDefault();  // Prevent default link behavior (page reload)
            confirmationModal.classList.add('show');
        });

        // Handle confirmation - Redirect to logout page if confirmed
        confirmLogout.addEventListener('click', function () {
            window.location.href = "logout.php"; // Redirect to logout page
        });

        // Close the modal without doing anything if canceled
        cancelLogout.addEventListener('click', function () {
            confirmationModal.classList.remove('show');
        });
    </script>
=======
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

>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
