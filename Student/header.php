<?php 
include './server.php';

if (!isset($_SESSION['idno'])) {
    echo "<script>alert('Session expired. Please log in again.'); window.location.href='Login.php';</script>";
    exit;
}

$idno = $_SESSION['idno'];
$query = "SELECT profile_picture FROM student WHERE idno = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $idno);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$profileImage = !empty($student['profile_picture']) ? 'uploads/'.$student['profile_picture'] : 'image/default.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Dashboard'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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

        .sidebar.collapsed .profile-img,
        .sidebar.collapsed .profile-placeholder {
            width: 50px;
            height: 50px;
        }

        .sidebar.collapsed .toggle-btn {
            margin-left: auto;
            margin-right: auto;
        }

        .profile-img, .profile-placeholder {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            transition: all 0.3s ease;
            background-color: #fff;
        }

        .profile-placeholder {
            background-color: #f8f9fa;
        }

        .sidebar.collapsed .profile-img,
        .sidebar.collapsed .profile-placeholder {
            width: 50px;
            height: 50px;
        }

        .sidebar .text-center {
            width: 100%;
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

        <!-- Profile Image -->
        <div class="text-center mb-3">
            <div class="d-flex justify-content-center">
                <?php if (!empty($student['profile_picture'])): ?>
                    <img src="<?= $profileImage ?>" class="profile-img border" alt="Profile Picture">
                <?php else: ?>
                    <div class="profile-placeholder d-flex align-items-center justify-content-center border">
                        <i class="fas fa-user fa-lg text-muted"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>


        <!-- Nav Links -->
        <nav class="nav flex-column">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link text-dark d-flex align-items-center">
                    <i class="fas fa-home me-2"></i> <span>Home</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="profile.php" class="nav-link text-dark d-flex align-items-center">
                    <i class="fas fa-user me-2"></i> <span>Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="resources.php" class="nav-link text-dark d-flex align-items-center">
                    <i class="fas fa-book me-2"></i> <span>Lab Resources</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="labschedule.php" class="nav-link text-dark d-flex align-items-center">
                    <i class="fas fa-calendar-alt me-2"></i> <span>Lab Schedules</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="reservation.php" class="nav-link text-dark d-flex align-items-center">
                    <i class="fas fa-briefcase me-2"></i> <span>Reservation</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="history.php" class="nav-link text-dark d-flex align-items-center">
                    <i class="fas fa-history me-2"></i> <span>Sit-in History</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="nav-link text-dark d-flex align-items-center">
                    <i class="fas fa-sign-out-alt me-2"></i> <span>Log Out</span>
                </a>
            </li>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 main-content p-4">
        <!-- Sidebar Toggle Script -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("collapsed");
        }
    </script>
