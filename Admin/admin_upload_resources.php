<?php
session_start();
include_once 'connection.php';
include 'navbar.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
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
        .main {
            margin-left: 140px;
            padding: 40px;
            width: calc(100% - 260px);
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
            margin-top: 10px;
        }
        .google-drive-btn {
            display: inline-block;
            margin-top: 10px;
            background-color: #4285F4;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
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
    <div class="main">
        <div class="card">
            <h2>Resource Management</h2>
            <p>My Drive / Shared Resources</p>
                <!-- Google Drive Button -->
                <a class="google-drive-btn" href="https://drive.google.com/drive/folders/18bx8UxVLv301SdCZqZNNu-uYfRhRhWAv?usp=drive_link" target="_blank">
                    <i class="fas fa-folder-open"></i> Open Google Drive
                </a>
            </div>
                <!-- Add more resource cards dynamically -->
            </div>
        </div>
    </div>
</body>
</html>
