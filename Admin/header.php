<?php
// Start the session only if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin'])) {
    header("Location: Login.php");
    exit();
}

// Prevent back button cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include '../student/connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #ffffff;
        }

        .sidebar {
            background-color: #E6E6FA;
            transition: all 0.3s;
            width: 250px;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar .nav-link span {
            transition: all 0.3s;
        }

        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .sidebar.collapsed .logo {
            display: none;
        }

        .sidebar.collapsed .toggle-btn {
            margin-left: auto;
            margin-right: auto;
        }

        html, body {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }

        .d-flex.vh-100 {
            height: calc(100% - 72px);
        }

        .flex-grow-1 {
            overflow-y: auto;
            padding: 20px;
        }

        .nav-link:hover {
            background-color: #d1c4e9;
            color: #000000 !important;
            border-radius: 5px;
        }

        .toggle-btn {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #333;
        }

        .sidebar .text-center {
            width: 100%;
        }
    </style>
</head>
<body>

<div class="d-flex vh-100">
    <!-- Sidebar -->
    <div id="sidebar" class="d-flex flex-column sidebar text-white p-3">
        <!-- Toggle Button -->
        <div class="text-end mb-3">
            <button class="toggle-btn" onclick="toggleSidebar()" title="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Admin Logo -->
        <div class="logo text-center fs-4 fw-bold py-3 text-primary-emphasis">ADMIN</div>

        <!-- Nav Links -->
        <nav class="nav flex-column">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link text-dark d-flex align-items-center">
                    <i class="fas fa-home me-2"></i> <span>Home</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="List-of-student.php" class="nav-link text-dark d-flex align-items-center">
                    <i class="fas fa-users me-2"></i> <span>List of Students</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="current-sit-in.php" class="nav-link text-dark d-flex align-items-center">
                    <i class="fas fa-users me-2"></i> <span>Current SIT IN</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="view-sit-in-records.php" class="nav-link text-dark d-flex align-items-center">
                    <i class="fas fa-list me-2"></i> <span>View Sit In Records</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="feedback-report.php" class="nav-link text-dark d-flex align-items-center">
                    <i class="fas fa-comments me-2"></i> <span>Feedback Report</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="view-reservation.php" class="nav-link text-dark d-flex align-items-center">
                    <i class="fas fa-calendar-alt me-2"></i> <span>View Reservation</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="resources.php" class="nav-link text-dark d-flex align-items-center">
                    <i class="fas fa-book me-2"></i> <span>Resources</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="labschedule.php" class="nav-link text-dark d-flex align-items-center">
                    <i class="fas fa-calendar-alt me-2"></i> <span>Lab Schedule</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="statistics.php" class="nav-link text-dark d-flex align-items-center">
                    <i class="fas fa-chart-pie me-2"></i> <span>Statistics</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="nav-link text-dark d-flex align-items-center">
                    <i class="fas fa-sign-out-alt me-2"></i> <span>Logout</span>
                </a>
            </li>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 main-content p-4">
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("collapsed");
        }
    </script>