<?php
<<<<<<< HEAD
session_start();
include './connection.php';

=======
session_start(); // Start the session
include './connection.php';

// Ensure session variables are set
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
if (!isset($_SESSION['idno'])) {
    header("Location: login.php");
    exit;
}
<<<<<<< HEAD
$currentPage = basename($_SERVER['PHP_SELF']);
$idno = $_SESSION['idno'];

=======

// Retrieve session values safely
$firstname = $_SESSION['firstname'] ?? '';
$lastname = $_SESSION['lastname'] ?? '';
$profile_picture = $_SESSION['profile_picture'] ?? 'de.jpg';

// Define userProfile array
$userProfile = [
    'firstname' => $firstname,
    'lastname' => $lastname,
    'profile_picture' => $profile_picture
];

// Handle profile picture upload
$upload_dir = "uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_picture"])) {
    if ($_FILES["profile_picture"]["error"] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES["profile_picture"]["type"];
        $file_name = basename($_FILES["profile_picture"]["name"]);
        $target_file = $upload_dir . $file_name;

        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $_SESSION['profile_picture'] = $target_file;
                $profile_picture = $target_file;
            }
        }
    }
}

// Database connection
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
$conn = new mysqli('localhost', 'root', '', 'sitin');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

<<<<<<< HEAD
// Fetch user data for profile 
$sqlUser = "SELECT * FROM student WHERE idno = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("i", $idno);
$stmtUser->execute();
$userResult = $stmtUser->get_result();
$userData = $userResult->fetch_assoc(); // Fetch the user data
$firstname = $userData['firstname'] ?? '';
$middlename = $userData['middlename'] ?? '';
$lastname = $userData['lastname'] ?? '';
$profile_picture = $userData['profile_picture'] ?? ''; // Fallback to default image if not found

// Fetch sit-in history with feedback data (this query retrieves multiple rows)
$sqlSitInHistory = "SELECT s.*, f.feedback AS feedback_text 
                    FROM sit_in s 
                    LEFT JOIN feedback f ON s.id = f.sit_in_id 
                    WHERE s.idno = ?";
$stmtSitInHistory = $conn->prepare($sqlSitInHistory);
$stmtSitInHistory->bind_param("i", $idno);
$stmtSitInHistory->execute();
$historyResult = $stmtSitInHistory->get_result();
$listPerson = $historyResult->fetch_all(MYSQLI_ASSOC); // Fetch the sit-in history

// Clean up
$stmtUser->close();
$stmtSitInHistory->close();
$conn->close();
?>

=======
// Handle reservation
$reservationSuccess = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitReserve'])) {
    $lab = $_POST['lab'] ?? '';
    $date = $_POST['date'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
    $reason = $_POST['reason'] ?? '';
    $language = $_POST['language'] ?? '';

    if ($lab && $date && $start_time && $end_time && $reason && $language) {
        $sql = "INSERT INTO reservations (user_id, lab, date, start_time, end_time, reason, language) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssss", $idno, $lab, $date, $start_time, $end_time, $reason, $language);

        if ($stmt->execute()) {
            $reservationSuccess = true;
        }
        $stmt->close();
    }
}
$conn->close();
?>
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
<<<<<<< HEAD
    <title>Sit-in History with Feedback</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script>
        function formatDate(date) {
            if (!date) return ''; // Return empty if date is not provided

            const options = { 
                year: 'numeric', 
                month: '2-digit', 
                day: '2-digit', 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit', 
                hour12: true 
            };

            // Create a new Date object from the given date string
            const dateObj = new Date(date);
            
            // Format the date using toLocaleString and options
            return dateObj.toLocaleString('en-US', options).replace(',', ' at');
        }

        function openFeedbackModal(sitinId, lab, purpose, login, logout, feedback = '') {
            document.getElementById("currentSitinId").value = sitinId;
            document.getElementById("labDetail").innerText = lab;
            document.getElementById("purposeDetail").innerText = purpose;
            document.getElementById("loginDetail").innerText = formatDate(login);
            document.getElementById("logoutDetail").innerText = formatDate(logout);

            const inputArea = document.getElementById("feedbackInputSection");
            const viewArea = document.getElementById("feedbackViewSection");
            const feedbackTextArea = document.getElementById("feedbackText");
            const errorMsg = document.getElementById("errorMsg");

            if (feedback !== '') {
                viewArea.style.display = "block";
                inputArea.style.display = "none";
                document.getElementById("feedbackDisplay").innerText = feedback;
            } else {
                inputArea.style.display = "block";
                viewArea.style.display = "none";
                feedbackTextArea.value = '';
            }

            errorMsg.innerText = '';
            document.getElementById("feedbackModal").style.display = "block";
        }

        function closeFeedbackModal() {
            document.getElementById("feedbackModal").style.display = "none";
        }

        function submitFeedback() {
            const feedback = document.getElementById("feedbackText").value.trim();
            const sitinId = document.getElementById("currentSitinId").value;
            const errorMsg = document.getElementById("errorMsg");

            if (feedback === "") {
                errorMsg.innerText = "Feedback cannot be empty.";
                return;
            }

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "submitfeedback.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    if (xhr.responseText.trim() === "success") {
                        closeFeedbackModal();
                        alert("Feedback submitted successfully.");
                        location.reload();  // Refresh the page to see the updated data
                    } else {
                        alert("Error submitting feedback.");
                    }
                }
            };

            // Send feedback and sitinId to PHP script
            xhr.send("feedback=" + encodeURIComponent(feedback) + "&sitin_id=" + sitinId);
        }

        window.onclick = function(event) {
            const modal = document.getElementById("feedbackModal");
            if (event.target === modal) {
                closeFeedbackModal();
            }
        };
    </script>
=======
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sit-in Monitoring Dashboard</title>
    <link rel="stylesheet" href="styles.css">
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            background-color: whitesmoke;
            margin: 0;
        }
        .sidebar {
<<<<<<< HEAD
            width: 200px;
            background-color: #6a0dad;
            color: white;
            height: 100vh;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
        }
=======
                width: 250px;
                background-color: purple;
                color: white;
                height: 100vh;
                padding: 20px;
                position: fixed; 
                top: 0;
                left: 0;
                overflow-y: auto; 
            }

>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
        .profile-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid white;
<<<<<<< HEAD
=======
            cursor: pointer;
        }
        .hidden-input {
            display: none;
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
<<<<<<< HEAD
=======
            width: 100%;
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
        }
        .sidebar ul li {
            padding: 15px;
            text-align: center;
<<<<<<< HEAD
=======
            transition: background 0.3s;
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
        }
        .sidebar ul li:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
<<<<<<< HEAD
        .sidebar ul li.active {
            background-color: rgba(255, 255, 255, 0.3);
            border-left: 5px solid white;
        }
        .sidebar ul li.active a {
            font-weight: bold;
            color: #fff;
        }
        .main-content { margin-left: 220px; padding: 40px; width: calc(100% - 220px); }
        .container { background: white; padding: 30px; border-radius: 10px; max-width: 900px; margin: auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .table-container { overflow-x: auto; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #6a0dad; color: white; }
        th, td { padding: 12px; text-align: center; border-bottom: 1px solid #ddd; }
        .feedback-btn { background: #673ab7; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; }
        .feedback-btn:hover { background: #512da8; }
        .feedback-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 50%; top: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            width: 400px;
        }
        .feedback-modal h2 { margin-top: 0; }
        .feedback-modal .detail { margin-bottom: 5px; font-size: 14px; }
        .feedback-modal textarea { width: 100%; height: 80px; margin-top: 10px; }
        .feedback-modal button { margin-top: 10px; margin-right: 10px; }
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: transparent;
            border: none;
            font-size: 30px;
            color: #000;
            cursor: pointer;
        }
        .close-btn:hover {
            color: red;
        }
        .error-msg { color: red; font-size: 13px; margin-top: 5px; }
=======
        .main-content {
            margin-left: 270px;
            padding: 40px;
            width: calc(100% - 270px);
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            width: 100%;
            max-width: 900px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            .main-content {
                margin-left: 220px;
                width: calc(100% - 220px);
            }
            .dashboard-cards {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
        }
         /* Global styles */
         body {
            font-family: Arial, sans-serif;
            background-color: #f7f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        h2 {
            text-align: center;
            color: #6d597a;
        }
        label {
            display: block;
            margin-top: 10px;
            color: #5c5470;
            font-weight: bold;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #d3c0d2;
            border-radius: 5px;
            font-size: 14px;
        }
        button {
            background-color: #b5838d;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 15px;
        }
        button:hover {
            background-color: #6d597a;
        }
        .success-message {
            text-align: center;
            color: green;
            font-weight: bold;
            margin-bottom: 10px;
        }
        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }
        }
          /* General Styles */
      body {
          font-family: 'Arial', sans-serif;
          background-color: #f5f5f5;
          margin: 0;
          padding: 0;
          display: flex;
          justify-content: center;
          align-items: center;
          height: 100vh; /* Center vertically */
      }

      /* Centering the container */
      .container {
          width: 70%;
          margin-left: 23%;
          margin-bottom: 30%;
          max-width: 1100px;
          background: #fff;
          padding: 10px;
          border-radius: 10px;
          box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
          text-align: center; /* Center content inside */
      }

      /* Title */
      .title {
          font-size: 24px;
          font-weight: bold;
          color: #4a148c;
          margin-bottom: 20px;
      }

      /* Table Styling */
      .table-container {
          overflow-x: auto;
          margin-top: 10px;
          display: flex;
          justify-content: center;
      }

      table {
          width: 100%;
          border-collapse: collapse;
          background: white;
          border-radius: 10px;
         
      }

      thead {
          background: #4a148c;
          color: white;
      }

      th, td {
          padding: 12px;
          text-align: center;
          border-bottom: 1px solid #ddd;
      }

      th {
          font-weight: bold;
      }

      tbody tr:hover {
          background-color: #f0e6fa;
          transition: 0.3s ease-in-out;
      }

      .no-data {
          text-align: center;
          padding: 15px;
          color: #888;
      }

      /* Feedback Button */
      .feedback-btn {
          background: #673ab7;
          color: white;
          border: none;
          padding: 8px 12px;
          border-radius: 5px;
          cursor: pointer;
          font-size: 14px;
          
          transition: 0.3s;
      }

      .feedback-btn:hover {
          background: #512da8;
      }

      /* Feedback Modal */
      .modal {
          display: none;
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(0, 0, 0, 0.5);
          display: flex;
          justify-content: center;
          align-items: center;
      }

      .modal-content {
          background: white;
          width: 90%;
          max-width: 400px;
          padding: 20px;
          border-radius: 10px;
          text-align: center;
          box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
          animation: fadeIn 0.3s ease-in-out;
      }

      .close {
          float: right;
          font-size: 20px;
          cursor: pointer;
      }

      .close:hover {
          color: red;
      }

      h2 {
          color: #4a148c;
          margin-bottom: 10px;
      }

      textarea {
          width: 100%;
          height: 100px;
          border: 1px solid #ddd;
          padding: 10px;
          border-radius: 5px;
          resize: none;
          font-size: 14px;
      }

      .submit-btn {
          background: #4a148c;
          color: white;
          border: none;
          padding: 10px 15px;
          margin-top: 10px;
          border-radius: 5px;
          cursor: pointer;
          transition: 0.3s;
      }

      .submit-btn:hover {
          background: #311b92;
      }

      /* Responsive Design */
      @media (max-width: 768px) {
          .container {
              width: 95%;
              padding: 15px;
          }

          th, td {
              font-size: 14px;
              padding: 8px;
          }

          .feedback-btn {
              font-size: 12px;
              padding: 6px 10px;
          }

          .modal-content {
              width: 90%;
              padding: 15px;
          }
      }

      /* Fade-in animation */
      @keyframes fadeIn {
          from { opacity: 0; transform: translateY(-10px); }
          to { opacity: 1; transform: translateY(0); }
      }

>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="profile-section">
<<<<<<< HEAD
            <?php if (empty($profile_picture)) { ?>
                <!-- Use Font Awesome Icon when profile picture is not available -->
                <i class="fas fa-user-circle fa-4x"></i>
            <?php } else { ?>
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="profile-pic">
            <?php } ?>
            
            <p>
                <?php 
                // Ensure names are not empty and handle middle name (first letter only)
                $full_name = !empty($firstname) && !empty($lastname) 
                            ? $firstname . " " . (empty($middlename) ? "" : ucfirst(strtolower($middlename[0])) . ". ") . $lastname 
                            : "Person";
                
                // Capitalize the first letter of each name (First and Last names)
                echo htmlspecialchars(ucwords(strtolower($full_name)));
                ?>
            </p>
        </div>
        <ul>
            <li class="<?= $currentPage == 'dashboard.php' ? 'active' : '' ?>"><a href="dashboard.php">Home</a></li>
            <li class="<?= $currentPage == 'profile.php' ? 'active' : '' ?>"><a href="profile.php">Edit Profile</a></li>
            <li class="<?= $currentPage == 'announcements.php' ? 'active' : '' ?>"><a href="announcements.php">Announcement</a></li>
            <li class="<?= $currentPage == 'SitinRules.php' ? 'active' : '' ?>"><a href="SitinRules.php">Sit-in Rules</a></li>
            <li class="<?= $currentPage == 'Labrules&Regulations.php' ? 'active' : '' ?>"><a href="Labrules&Regulations.php">Lab Rules</a></li>
            <li class="<?= $currentPage == 'Reservation.php' ? 'active' : '' ?>"><a href="Reservation.php">Reservation</a></li>
            <li class="<?= $currentPage == 'SitinHistory.php' ? 'active' : '' ?>"><a href="SitinHistory.php">Sit-in History</a></li>
            <li class="<?= $currentPage == 'LabResource.php' ? 'active' : '' ?>"><a href="LabResource.php">View Lab Resource</a></li>
            <li class="<?= $currentPage == 'ViewSession.php' ? 'active' : '' ?>"><a href="ViewSession.php">Session</a></li>
            <li class="<?= $currentPage == 'Leaderboard.php' ? 'active' : '' ?>"><a href="Leaderboard.php">Leaderboard</a></li>
            <li class="<?= $currentPage == 'LabSchedule.php' ? 'active' : '' ?>"><a href="LabSchedule.php">Lab Schedule</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Feedback Modal -->
    <div class="feedback-modal" id="feedbackModal">
        <!-- Close button in top-right of modal -->
        <button onclick="closeFeedbackModal()" class="close-btn">&times;</button>

        <h2>Feedback</h2>
        <input type="hidden" id="currentSitinId">
        <div class="detail"><strong>Laboratory:</strong> <span id="labDetail"></span></div>
        <div class="detail"><strong>Purpose:</strong> <span id="purposeDetail"></span></div>
        <div class="detail"><strong>Sit-in Time:</strong> <span id="loginDetail"></span></div>
        <div class="detail"><strong>Sit-out Time:</strong> <span id="logoutDetail"></span></div>

        <div id="feedbackViewSection" style="margin-top: 15px;">
            <strong>Feedback Given:</strong>
            <p id="feedbackDisplay" style="white-space: pre-wrap;"></p>
        </div>

        <div id="feedbackInputSection">
            <textarea id="feedbackText" placeholder="Write your feedback here..."></textarea>
            <div class="error-msg" id="errorMsg"></div>
            <button onclick="submitFeedback()">Submit</button>
            <button onclick="closeFeedbackModal()">Cancel</button>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <h1>Sit-in History</h1>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Laboratory</th>
                            <th>Purpose</th>
                            <th>Login</th>
                            <th>Logout</th>
                            <th>Feedback</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($listPerson) && count($listPerson) > 0) : ?>
                            <?php foreach ($listPerson as $person) : ?>
                                <?php
                                // Function to format the date
                                function formatDate($date) {
                                    if (empty($date)) {
                                        return ''; // Return an empty string if no date is provided
                                    }
                                    
                                    try {
                                        $datetime = new DateTime($date); // Create a DateTime object from the given date
                                        return $datetime->format('m/d/Y \a\t h:i A'); // Format the date in MM/DD/YYYY at HH:MM AM/PM
                                    } catch (Exception $e) {
                                        return ''; // Return an empty string if the date is invalid
                                    }
                                }
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($person['laboratory'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($person['purpose'] ?? '') ?></td>
                                    <td><?= htmlspecialchars(formatDate($person['sitin_time'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars(formatDate($person['sit_out_time'] ?? '')) ?></td>
                                    <td>
                                        <?php if (!empty($person['feedback_text'])) : ?>
                                            <button class="feedback-btn" onclick="openFeedbackModal(
                                                <?= $person['id'] ?>,
                                                '<?= addslashes($person['laboratory']) ?>',
                                                '<?= addslashes($person['purpose']) ?>',
                                                '<?= $person['sitin_time'] ?>',
                                                '<?= $person['sit_out_time'] ?>',
                                                `<?= addslashes($person['feedback_text']) ?>`
                                            )">View Feedback</button>
                                        <?php elseif (!empty($person['sit_out_time'])) : ?>
                                            <button class="feedback-btn" onclick="openFeedbackModal(
                                                <?= $person['id'] ?>,
                                                '<?= addslashes($person['laboratory']) ?>',
                                                '<?= addslashes($person['purpose']) ?>',
                                                '<?= $person['sitin_time'] ?>',
                                                '<?= $person['sit_out_time'] ?>'
                                            )">Give Feedback</button>
                                        <?php else : ?>
                                            <button class="feedback-btn" disabled>Pending</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td colspan="5">No sit-in history found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
=======
      <!-- Replace $userProfile with the correct variables -->
<img src="<?php echo htmlspecialchars($profile_picture); ?>" 
     alt="Profile Picture" class="profile-pic" id="display-pic">

<p><?php echo htmlspecialchars($firstname . " " . $lastname); ?></p>
        </div>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="SitinRules.php">Sit-in Rules</a></li>
            <li><a href="Labrules&Regulations.php">Lab Rules & Regulations</a></li>
            <li><a href="announcements.php">Announcement</a></li>
            <li><a href="Reservation.php">Reservation</a></li>
            <li><a href="SitinHistory.php">Sit-in History</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="container">
        <h1 class="title">Sit-in History</h1>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID Number</th>
                        <th>Name</th>
                        <th>Purpose</th>
                        <th>Laboratory</th>
                        <th>Login</th>
                        <th>Logout</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($listPerson)) : ?>
                        <?php foreach ($listPerson as $person) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($person['id_number'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars(($person['firstName'] ?? '') . " " . ($person['lastName'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars($person['sit_purpose'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($person['sit_lab'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($person['sit_login'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($person['sit_logout'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($person['sit_date'] ?? ''); ?></td>
                                <td>
                                    <button class="feedback-btn" 
                                            data-id="<?php echo htmlspecialchars($person['id_number'] ?? ''); ?>" 
                                            data-lab="<?php echo htmlspecialchars($person['sit_lab'] ?? ''); ?>">
                                        Feedback
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="8" class="no-data">No history available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Feedback Modal -->
    <div id="feedbackModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Feedback Experience</h2>
            <form action="process_feedback.php" method="POST">
                <input type="hidden" id="id_number" name="id_number">
                <input type="hidden" id="sit_lab" name="sit_lab">
                <textarea name="feedback_text" required placeholder="Tell us about your experience..."></textarea>
                <button type="submit" name="submit_feedback" class="submit-btn">Submit Feedback</button>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
</body>
</html>
