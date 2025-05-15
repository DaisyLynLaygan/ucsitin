<?php
include './server.php';
include 'header.php';

if (!isset($_SESSION['idno'])) {
    echo "<script>alert('Session expired. Please log in again.'); window.location.href='login.php';</script>";
    exit;
}

$idno = $_SESSION['idno'];
$query = "SELECT * FROM student WHERE idno = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $idno);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
?>

<style>
    body {
        background-color: #f8f9fa;
        color: #303f9f;
    }
    .profile-main-wrapper {
        max-width: 1100px;
        margin: 40px auto;
        background: #fff;
        border-radius: 0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        padding: 40px 30px 30px 30px;
    }
    .profile-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 40px;
        gap: 30px;
    }
    .profile-user-info {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .profile-image-container {
        width: 80px;
        height: 80px;
        border-radius: 0;
        border: 2px solid #e9ecef;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        cursor: pointer;
    }
    .profile-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .profile-image-placeholder {
        font-size: 40px;
        color: #303f9f;
    }
    .profile-basic {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .profile-basic .profile-name {
        font-size: 20px;
        font-weight: 600;
        color: #303f9f;
    }
    .profile-basic .profile-email {
        font-size: 15px;
        color: #303f9f;
        opacity: 0.8;
    }
    .edit-btn {
        background: #303f9f;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 8px 28px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s;
        margin-left: auto;
    }
    .edit-btn:hover {
        background: #1a252f;
    }
    .profile-form-section {
        margin-top: 10px;
    }
    .profile-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px 32px;
    }
    .profile-form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .form-label {
        font-weight: 500;
        color: #303f9f;
        font-size: 15px;
    }
    .form-control {
        border: 1px solid #ced4da;
        border-radius: 5px;
        padding: 10px 15px;
        font-size: 15px;
        color: #303f9f;
        background: #fafbfc;
    }
    .form-control:focus {
        border-color: #303f9f;
        box-shadow: 0 0 0 0.15rem rgba(48, 63, 159, 0.15);
    }
    .btn-save {
        background-color: #303f9f;
        border: none;
        padding: 12px;
        font-weight: 500;
        border-radius: 5px;
        transition: all 0.3s;
        color: #fff;
        font-size: 16px;
        margin-top: 30px;
        width: 100%;
    }
    .btn-save:hover {
        background-color: #1a252f;
    }
    .upload-hint {
        text-align: left;
        color: #303f9f;
        font-size: 13px;
        margin-top: 5px;
        margin-left: 2px;
    }
    @media (max-width: 900px) {
        .profile-main-wrapper {
            padding: 20px 5px;
        }
        .profile-top {
            flex-direction: column;
            align-items: flex-start;
            gap: 18px;
        }
        .profile-form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="profile-main-wrapper">
    <div class="profile-top">
        <div class="profile-user-info">
            <div class="profile-image-container" onclick="document.getElementById('profileImageUpload').click();">
                <?php if (!empty($student['profile_picture'])): ?>
                    <img id="profilePreview" src="../student/uploads/<?= htmlspecialchars($student['profile_picture']) ?>" class="profile-image" alt="Profile Picture">
                <?php else: ?>
                    <div class="profile-image-placeholder">
                        <i class="fas fa-user"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="profile-basic">
                <span class="profile-name"><?= htmlspecialchars($student['firstname'] . ' ' . $student['lastname']) ?></span>
                <span class="profile-email"><?= htmlspecialchars($student['email']) ?></span>
            </div>
        </div>
        <button type="button" class="edit-btn" onclick="document.getElementById('profileForm').scrollIntoView({behavior: 'smooth'});">Edit</button>
    </div>
    <form method="post" action="server.php" enctype="multipart/form-data" id="profileForm" class="profile-form-section">
        <input type="file" class="d-none" id="profileImageUpload" name="profile_picture" accept="image/*" onchange="previewImage(event)">
        <div class="upload-hint">Click the photo to upload/change</div>
        <div class="profile-form-grid">
            <div class="profile-form-group">
                <label class="form-label">ID Number</label>
                <input type="text" class="form-control" name="idno" value="<?= htmlspecialchars($student['idno'] ?? '') ?>" readonly>
            </div>
            <div class="profile-form-group">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($student['username'] ?? '') ?>" readonly>
            </div>
            <div class="profile-form-group">
                <label class="form-label">First Name</label>
                <input type="text" class="form-control" name="firstname" value="<?= htmlspecialchars($student['firstname'] ?? '') ?>" required>
            </div>
            <div class="profile-form-group">
                <label class="form-label">Last Name</label>
                <input type="text" class="form-control" name="lastname" value="<?= htmlspecialchars($student['lastname'] ?? '') ?>" required>
            </div>
            <div class="profile-form-group">
                <label class="form-label">Middle Name</label>
                <input type="text" class="form-control" name="middlename" value="<?= htmlspecialchars($student['middlename'] ?? '') ?>">
            </div>
            <div class="profile-form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($student['email'] ?? '') ?>" required>
            </div>
            <div class="profile-form-group">
                <label class="form-label">Course</label>
                <input type="text" class="form-control" name="course" value="<?= htmlspecialchars($student['course'] ?? '') ?>" required>
            </div>
            <div class="profile-form-group">
                <label class="form-label">Year Level</label>
                <input type="text" class="form-control" name="yearlevel" value="<?= htmlspecialchars($student['yearlevel'] ?? '') ?>" required>
            </div>
        </div>
        <button type="submit" class="btn btn-save">
            <i class="fas fa-save me-2"></i>Save Changes
        </button>
    </form>
</div>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const preview = document.getElementById('profilePreview');
        if (preview) {
            preview.src = reader.result;
        } else {
            // Create image element if it doesn't exist (for first upload)
            const container = document.querySelector('.profile-image-container');
            container.innerHTML = `<img id="profilePreview" src="${reader.result}" class="profile-image" alt="Profile Picture">`;
        }
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
