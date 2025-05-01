<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'connection.php';
    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $availability = isset($_POST['availability']) ? $_POST['availability'] : '';

    if (isset($_FILES['resource_file']) && $_FILES['resource_file']['error'] === 0) {
        $filename = basename($_FILES["resource_file"]["name"]);
        $filesize = $_FILES["resource_file"]["size"];
        $filetmp = $_FILES["resource_file"]["tmp_name"];
        $upload_dir = "uploads/";

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filepath = $upload_dir . $filename;

        if (move_uploaded_file($filetmp, $filepath)) {
            $stmt = $conn->prepare("INSERT INTO resources (title, filename, size, description, availability) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiss", $title, $filename, $filesize, $description, $availability);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            header("Location: admin_upload_resources.php?upload=success");
            exit();
        } else {
            echo "Failed to move uploaded file.";
        }
    } else {
        echo "No file uploaded or file error.";
    }

    $conn->close();
} else {
    header("Location: admin_upload_resources.php");
    exit();
}
?>
