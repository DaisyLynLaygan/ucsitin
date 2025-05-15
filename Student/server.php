<?php
// Start the session only if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("connection.php");

// Registration
if (isset($_POST['SubmitForm'])) {
    $idno = $_POST['idno'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $middlename = $_POST['middlename'];
    $course = $_POST['course'];
    $yearlevel = $_POST['yearlevel'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Secure password hashing

    // Check if the ID number already exists
    $checkSql = "SELECT idno FROM student WHERE idno = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $idno);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo "<script>alert('ID number already exists. Please use a different ID number.'); window.location.href='register.php';</script>";
        exit();
    }

    // Insert data into the student table
    $sql = "INSERT INTO student (idno, firstname, lastname, middlename, course, yearlevel, email, username, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $idno, $firstname, $lastname, $middlename, $course, $yearlevel, $email, $username, $password);

    if ($stmt->execute()) {
        // After successful registration, insert 30 remaining sessions
        $sessionSql = "INSERT INTO student_session (idno, remaining_session) VALUES (?, 30)";
        $sessionStmt = $conn->prepare($sessionSql);
        $sessionStmt->bind_param("s", $idno);

        if ($sessionStmt->execute()) {
            $_SESSION['success'] = "Registration successful! 30 sessions have been added.";
            echo "<script>alert('Registration successful! 30 sessions have been added.'); window.location.href='Login.php';</script>";
        } else {
            $error = "Error inserting sessions: " . $conn->error;
            echo "<script>alert('Registration successful but failed to assign sessions: $error'); window.location.href='Login.php';</script>";
        }

        $sessionStmt->close();
        exit();
    } else {
        $error = "Error: " . $conn->error;
        echo "<script>alert('Registration failed: $error'); window.location.href='register.php';</script>";
        exit();
    }
}

// Login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM student WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['idno'] = $user['idno'];  // ✅ Correct session key
            $_SESSION['username'] = $user['username'];
            echo "<script>alert('Login successful! Redirecting to dashboard...'); window.location.href='dashboard.php';</script>";
            exit();

        } else {
            echo "<script>alert('Invalid username or password. Please try again.'); window.location.href='Login.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid username or password. Please try again.'); window.location.href='Login.php';</script>";
    }
}

// Update Profile
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idno'])) {
    $idno = $_POST['idno'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $middlename = $_POST['middlename'] ?? '';
    $email = $_POST['email'];
    $course = $_POST['course'];
    $yearlevel = $_POST['yearlevel'];

    // Fetch existing profile picture
    $query = "SELECT profile_picture FROM student WHERE idno = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $idno);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $profilePicture = $student['profile_picture'] ?? '';

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = time() . '_' . basename($_FILES['profile_picture']['name']); // Unique name
        $uploadDir = 'uploads/';
        $destPath = $uploadDir . $fileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
        }

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $profilePicture = $fileName; // Save only the file name
        }
    }

    // Update student record
    $sql = "UPDATE student SET firstname=?, lastname=?, middlename=?, email=?, course=?, yearlevel=?, profile_picture=? WHERE idno=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssss', $firstname, $lastname, $middlename, $email, $course, $yearlevel, $profilePicture, $idno);

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";
    } else {
        echo "<script>alert('Failed to update profile.'); window.location.href='profile.php';</script>";
    }

    
}
?>


