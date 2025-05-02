<?php
session_start();
include './connection.php';

if (!isset($_SESSION['idno'])) {
    header("Location: login.php");
    exit;
}
$currentPage = basename($_SERVER['PHP_SELF']);
$idno = $_SESSION['idno'];

$conn = new mysqli('localhost', 'root', '', 'sitin');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
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
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            background-color: whitesmoke;
            margin: 0;
        }
        .sidebar {
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
        .profile-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid white;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            padding: 15px;
            text-align: center;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
        }
        .sidebar ul li:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
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
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="profile-section">
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
        <li><a href="dashboard.php">Home</a></li>
            <li><a href="profile.php">Edit Profile</a></li>
            <li><a href="announcements.php">View Announcements</a></li>
            <li><a href="SitinRules.php">Sit-in Rules</a></li>
            <li><a href="Reservation.php">Reservation</a></li>
            <li><a href="SitinHistory.php">Sit-in History</a></li>
            <li><a href="LabResources.php">View Lab Resources</a></li>
            <li><a href="ViewSession.php">Session</a></li>
            <li><a href="Leaderboard.php">Leaderboard</a></li>
            <li><a href="LabSchedule.php">Lab Schedule</a></li>
            <li><a href="logout.php">Log Out</a></li>
    </ul>
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
</body>
</html>
