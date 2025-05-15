<?php
include '../student/connection.php';
include 'header.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('Invalid request.'); window.location.href='List-of-student.php';</script>";
    exit;
}

$idno = $_GET['id'];
$query = "SELECT * FROM student WHERE idno = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $idno);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "<script>alert('Student not found.'); window.location.href='List-of-student.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $middlename = $_POST['middlename'];
    $course = $_POST['course'];
    $yearlevel = $_POST['yearlevel'];
    $email = $_POST['email'];
    $username = $_POST['username'];

    $updateQuery = "UPDATE student SET firstname=?, lastname=?, middlename=?, course=?, yearlevel=?, email=?, username=? WHERE idno=?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param('ssssssss', $firstname, $lastname, $middlename, $course, $yearlevel, $email, $username, $idno);

    if ($updateStmt->execute()) {
        // Handle profile picture upload
        if (!empty($_FILES['profile_picture']['name'])) {
            $profilePic = basename($_FILES['profile_picture']['name']);
            $targetDir = "../student/uploads/";
            $targetFile = $targetDir . $profilePic;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Validate file type
            $allowedTypes = ['jpg', 'jpeg', 'png'];
            if (in_array($imageFileType, $allowedTypes)) {
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
                    $updatePicQuery = "UPDATE student SET profile_picture=? WHERE idno=?";
                    $picStmt = $conn->prepare($updatePicQuery);
                    $picStmt->bind_param('ss', $profilePic, $idno);
                    $picStmt->execute();
                } else {
                    echo "<script>alert('Error uploading file.');</script>";
                }
            } else {
                echo "<script>alert('Invalid file type. Only JPG, JPEG, and PNG are allowed.');</script>";
            }
        }
        echo "<script>alert('Student updated successfully!'); window.location.href='List-of-student.php';</script>";
    } else {
        echo "<script>alert('Error updating student.');</script>";
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg p-4">
                <h2 class="text-center mb-4">Edit Student</h2>
                <!-- Cancel (X) Button -->
                <a href="List-of-student.php" class="position-absolute top-0 end-0 mt-2 me-2 text-dark text-decoration-none" 
                style="font-size: 24px; font-weight: bold;">&times;</a>
                        <form method="POST" enctype="multipart/form-data">
                    
                <!-- Profile Picture Upload & Preview -->
                    <div class="mb-3 d-flex flex-column align-items-center">
                        <div id="profileImageContainer" 
                            class="rounded-circle overflow-hidden d-flex justify-content-center align-items-center"
                            style="width: 120px; height: 120px; border: 2px solid #ccc; cursor: pointer; background-size: cover; background-position: center;"
                            onclick="document.getElementById('profileImageUpload').click();">
                            <img id="profilePreview"
                                src="<?= !empty($student['profile_picture']) ? '../student/uploads/' . $student['profile_picture'] : 'https://via.placeholder.com/150' ?>"
                                class="rounded-circle" width="100%" height="100%" alt="Profile Picture">
                        </div>
                        <input type="file" class="d-none" id="profileImageUpload" name="profile_picture" accept="image/*" onchange="previewImage(event)">
                        <small class="text-muted mt-2">Click the photo to upload/change</small>
                    </div>

                    <!-- JavaScript for Image Preview -->
                    <script>
                    function previewImage(event) {
                        const reader = new FileReader();
                        reader.onload = function () {
                            document.getElementById('profilePreview').src = reader.result;
                        };
                        reader.readAsDataURL(event.target.files[0]);
                    }
                    </script>


                    <!-- ID Number (Non-Editable) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">ID Number</label>
                        <input type="text" class="form-control" value="<?= $student['idno']; ?>" disabled>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">First Name</label>
                            <input type="text" class="form-control" name="firstname" value="<?= $student['firstname']; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Last Name</label>
                            <input type="text" class="form-control" name="lastname" value="<?= $student['lastname']; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Middle Name</label>
                            <input type="text" class="form-control" name="middlename" value="<?= $student['middlename']; ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Course</label>
                            <select class="form-select" name="course" required>
                                <option value="BSIT" <?= $student['course'] == 'BSIT' ? 'selected' : ''; ?>>BSIT</option>
                                <option value="BSCpE" <?= $student['course'] == 'BSCpE' ? 'selected' : ''; ?>>BSCpE</option>
                                <option value="BSBA" <?= $student['course'] == 'BSBA' ? 'selected' : ''; ?>>BSBA</option>
                                <option value="BSCS" <?= $student['course'] == 'BSCS' ? 'selected' : ''; ?>>BSCS</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Year Level</label>
                            <select class="form-select" name="yearlevel" required>
                                <option value="1" <?= $student['yearlevel'] == '1' ? 'selected' : ''; ?>>1st Year</option>
                                <option value="2" <?= $student['yearlevel'] == '2' ? 'selected' : ''; ?>>2nd Year</option>
                                <option value="3" <?= $student['yearlevel'] == '3' ? 'selected' : ''; ?>>3rd Year</option>
                                <option value="4" <?= $student['yearlevel'] == '4' ? 'selected' : ''; ?>>4th Year</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" class="form-control" name="email" value="<?= $student['email']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Username</label>
                        <input type="text" class="form-control" name="username" value="<?= $student['username']; ?>" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Function to preview the uploaded profile picture
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        document.getElementById('profilePreview').src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
