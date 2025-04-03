<?php
session_start();
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

        .sidebar ul li:hover {
            background-color: rgba(255, 255, 255, 0.15);
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
    </style>
</head>
<body>
    <div class="sidebar">
        <ul>
        <li><a href="dashboard.php"><i class="fas fa-user-shield"></i> Admin</a></li>
        <li><a href="Search.php"><i class="fas fa-search"></i> Search</a></li>
        <li><a href="ListofStudents.php"><i class="fas fa-users"></i> List of Students</a></li>
        <li><a href="Announcement.php"><i class="fas fa-bullhorn"></i> Announcement</a></li>
        <li><a href="current-sitin.php"><i class="fas fa-user-clock"></i> View Current Sit-in</a></li>
        <li><a href="sitinRecord.php"><i class="fas fa-clipboard-list"></i> Sit-in Report</a></li>
        <li><a href="FeedbackReports.php"><i class="fas fa-comment-dots"></i> Feedback Reports</a></li>
        <li><a href="view-reservation.php"><i class="fas fa-calendar-check"></i> View Reservations</a></li>
        <li><a href="statistics.php"><i class="fas fa-chart-pie"></i> Statistics</a></li>
        <li><a href="daily-analytics.php"><i class="fas fa-chart-line"></i> Daily Analytics</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
         </ul>
    </div>
    <div class="main-content">
