<?php
// Start session and check if admin is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include 'connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sit-in Monitoring Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
       body {
            display: flex;
            font-family: Arial, sans-serif;
            background-color: whitesmoke;
            margin: 0;
        }
        .sidebar {
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

        .profile-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid white;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            text-align: left;
            padding: 10px 20px;
            transition: background 0.3s ease;
        }

        .sidebar ul li a {
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            font-size: 15px;
        }

        .sidebar ul li a i {
            margin-right: 10px;
            min-width: 20px;
            text-align: center;
            font-size: 16px;
        }

        .sidebar ul li.active {
            background-color:rgb(98, 58, 119); 
            color: white;
            border-radius: 5px;
            font-weight: bold; 
        }

        .sidebar ul li:hover {
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 5px;
        }

        .sidebar_logout:hover {
            cursor: pointer;
            background-color: red !important;
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
        @media (max-width: 768px) 
            .sidebar {
                width: 200px;
            }
            .main-content {
                margin-left: 220px;
                width: calc(100% - 220px);
            }

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
            }
        }
    </style>
</head>
<body>
<!-- basename($_SERVER['PHP_SELF']) is a PHP function used to get the filename of the currently executing script. --> 
    <div class="sidebar">
        <ul>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>"><a href="dashboard.php" ><i class="fas fa-user-shield"></i> Admin</a></li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'Search.php' ? 'active' : ''; ?>"><a href="Search.php"><i class="fas fa-search"></i> Search</a></li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'ListofStudents.php' ? 'active' : ''; ?>"><a href="ListofStudents.php" ><i class="fas fa-users"></i> List of Students</a></li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'Announcement.php' ? 'active' : ''; ?>"><a href="Announcement.php"><i class="fas fa-bullhorn"></i> Announcement</a></li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'current-sitin.php' ? 'active' : ''; ?>"><a href="current-sitin.php"><i class="fas fa-user-clock"></i> View Current Sit-in</a></li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'sitinRecord.php' ? 'active' : ''; ?>"><a href="sitinRecord.php"><i class="fas fa-clipboard-list"></i> Sit-in Report</a></li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'FeedbackReports.php' ? 'active' : ''; ?>"><a href="FeedbackReports.php"><i class="fas fa-comment-dots"></i> Feedback Reports</a></li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'view-reservation.php' ? 'active' : ''; ?>"><a href="view-reservation.php"><i class="fas fa-calendar-check"></i> View Reservations</a></li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'statistics.php' ? 'active' : ''; ?>"><a href="statistics.php"><i class="fas fa-chart-pie"></i> Statistics</a></li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'daily-analytics.php' ? 'active' : ''; ?>"><a href="daily-analytics.php"><i class="fas fa-chart-line"></i> Daily Analytics</a></li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'Lab-management.php' ? 'active' : ''; ?>"><a href="Lab-management.php"><i class="fas fa-chart-line"></i>Lab-Management</a></li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'ReservationLog.php' ? 'active' : ''; ?>"><a href="ReservationLog.php"><i class="fas fa-chart-line"></i>Reservation Log</a></li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'Lab Schedule.php' ? 'active' : ''; ?>"><a href="Lab Schedule.php"><i class="fas fa-chart-line"></i>Lab Schedule</a></li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'Leaderboard.php' ? 'active' : ''; ?>"><a href="Leaderboard.php"><i class="fas fa-chart-line"></i>Leaderboard</a></li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'upload_resources.php' ? 'active' : ''; ?>"><a href="upload_resources.php"><i class="fas fa-chart-line"></i>upload resources</a></li>
            <li class="sidebar_logout"><a href="javascript:void(0);" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="main-content"> <!-- Note: This has no closing div since this will render on another php file meaning the closing div of this is in there --> 
        <div id="confirmationModal">
            <div class="modal-content">
                <h3>Are you sure you want to log out?</h3>
                <button class="confirm" id="confirmLogout">Yes</button>
                <button class="cancel" id="cancelLogout">No</button>
            </div>
        </div>

    <script>
        // Get the modal and buttons. This is for logout confirmation
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