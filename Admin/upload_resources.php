<?php
session_start();
include_once 'connection.php'; // ensures connection variables are loaded
include 'navbar.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Resources</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: whitesmoke;
            color: #333;
        }

        .sidebar {
            width: 250px;
            background-color: #6a0dad; /* Dark purple */
            height: 100vh;
            position: fixed;
            padding: 20px 0;
            color: white;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            transition: 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #b57edc; /* Light purple */
        }

        .main {
            margin-left: 250px;
            padding: 20px;
        }

        .card {
            background-color: #f5f3f9;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .upload-section {
            border: 2px dashed #6a0dad;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
            background-color: #fdfaff;
            margin-bottom: 20px;
        }

        .upload-section input,
        .upload-section select,
        .upload-section textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .upload-section button {
            background-color: #6a0dad;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .resource-list {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .resource-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            width: 200px;
        }

        .resource-card i {
            font-size: 40px;
            margin-bottom: 10px;
            color: #6a0dad;
        }

        .resource-card .filename {
            font-weight: bold;
        }

        .resource-card button {
            margin: 5px;
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-download {
            background-color: #6a0dad;
            color: white;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="#">Dashboard</a>
        <a href="#">Current Sit-in Records</a>
        <a href="#">Sit-in Reports</a>
        <a href="#">Feedback Reports</a>
        <a href="#">Manage Sit-ins</a>
        <a href="#">Announcements</a>
        <a href="#">List of Students</a>
        <a href="#">Reservations Requests</a>
        <a href="#">Reservation Logs</a>
        <a href="#" class="active">Upload Resources</a>
        <a href="#">Leaderboard</a>
        <a href="#">Lab Schedule</a>
        <a href="#">Lab Management</a>
        <a href="#">Log Out</a>
    </div>

    <div class="main">
        <div class="card">
            <h2>Resource Management</h2>
            <p>My Drive / Shared Resources</p>
            <div class="upload-section">
                <form action="upload_resource.php" method="POST" enctype="multipart/form-data">
                    <p><strong>Drag & drop files here or click to browse</strong></p>
                    <input type="file" name="resource_file" required>
                    <input type="text" name="title" placeholder="Title *" required>
                    <textarea name="description" placeholder="Description (Optional)"></textarea>
                    <select name="availability">
                        <option value="All Users">All Users</option>
                        <option value="Staff Only">Staff Only</option>
                        <option value="Students Only">Students Only</option>
                    </select>
                    <button type="submit">Upload Resource</button>
                </form>
            </div>
            <div class="resource-list">
                <div class="resource-card">
                    <i class="fas fa-file-alt"></i>
                    <div class="filename">jovan bi</div>
                    <div>201.97 KB</div>
                    <button class="btn-download">Download</button>
                    <button class="btn-delete">Delete</button>
                </div>
                <div class="resource-card">
                    <i class="fas fa-file-alt"></i>
                    <div class="filename">Pic</div>
                    <div>141.83 KB</div>
                    <button class="btn-download">Download</button>
                    <button class="btn-delete">Delete</button>
                </div>
                <div class="resource-card">
                    <i class="fas fa-file-alt"></i>
                    <div class="filename">jeffl</div>
                    <div>1.39 KB</div>
                    <button class="btn-download">Download</button>
                    <button class="btn-delete">Delete</button>
                </div>
                <div class="resource-card">
                    <i class="fas fa-file-alt"></i>
                    <div class="filename">Ronzkie bb</div>
                    <div>15 bytes</div>
                    <button class="btn-download">Download</button>
                    <button class="btn-delete">Delete</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
