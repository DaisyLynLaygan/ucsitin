<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $middlename = $_POST['middlename'];

    // Handle profile picture upload
    if (!empty($_FILES['profile_picture']['name'])) {
        $upload_dir = "uploads/";
        $profile_pic = $upload_dir . basename($_FILES["profile_picture"]["name"]);

        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $profile_pic)) {
            $query = $conn->prepare("UPDATE student SET profile_picture = ? WHERE id = ?");
            $query->bind_param("si", $profile_pic, $user_id);
            $query->execute();
        }
    }

    // Update other profile details
    $query = $conn->prepare("UPDATE student SET firstname = ?, lastname = ?, middlename = ? WHERE id = ?");
    $query->bind_param("sssi", $firstname, $lastname, $middlename, $user_id);
    
    if ($query->execute()) {
        header("Location: profile.php");
        exit();
    } else {
        echo "Error updating profile.";
    }
}
?>
