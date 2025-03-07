<?php
include 'navbar.php';

// Ensure only the admin can access this page
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Handle announcement submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $date_posted = date("Y-m-d H:i:s");

    if (!empty($title) && !empty($content)) {
        $stmt = $conn->prepare("INSERT INTO announcements (title, content, date_posted) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $content, $date_posted);

        if ($stmt->execute()) {
            echo "<script>alert('Announcement posted successfully!'); window.location='Announcement.php';</script>";
        } else {
            echo "<script>alert('Error posting announcement.');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Title and content cannot be empty!');</script>";
    }
}

$conn->close();
?>
    <style>

        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 50%;
            text-align: center;
        }
        h2 {
            color: purple;
        }
        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .input-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .input-group input, .input-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            background-color: purple;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        .btn:hover {
            background-color: darkviolet;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin - Create Announcement</h2>
        <form method="POST">
            <div class="input-group">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" required>
            </div>
            <div class="input-group">
                <label for="content">Content:</label>
                <textarea name="content" id="content" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn">Post Announcement</button>
        </form>

    </div>
</body>
</html>
