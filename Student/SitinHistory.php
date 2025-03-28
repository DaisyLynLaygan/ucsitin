<?php
session_start();
include './connection.php';

// Ensure user is logged in
if (!isset($_SESSION['idno'])) {
    header("Location: login.php");
    exit;
}
$idno = $_SESSION['idno'];
$firstname = $_SESSION['firstname'] ?? '';
$lastname = $_SESSION['lastname'] ?? '';
$profile_picture = $_SESSION['profile_picture'] ?? 'de.jpg';

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'sitin');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch sit-in history for the logged-in user
$sql = "SELECT * FROM sit_in WHERE idno = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idno);
$stmt->execute();
$result = $stmt->get_result();
$listPerson = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sit-in Monitoring Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <head>
    <script>
    function openFeedbackModal() {
        document.getElementById("feedbackModal").style.display = "block";
    }

    function closeFeedbackModal() {
        document.getElementById("feedbackModal").style.display = "none";
    }

    function submitFeedback() {
        let feedback = document.getElementById("feedbackText").value;
        if (feedback.trim() === "") {
            alert("Feedback cannot be empty.");
            return;
        }

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "submit_feedback.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                if (xhr.responseText.trim() === "success") {
                    alert("Feedback successfully submitted");
                    closeFeedbackModal(); // Close modal on success
                    location.reload();
                } else {
                    alert("Error submitting feedback.");
                }
            }
        };
        xhr.send("feedback=" + encodeURIComponent(feedback) + "&idno=<?php echo $idno; ?>");
    }

    // Close the modal if user clicks outside of it
    window.onclick = function(event) {
        let modal = document.getElementById("feedbackModal");
        if (event.target === modal) {
            closeFeedbackModal();
        }
    };
</script>
</head>
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
        .main-content {
            margin-left: 220px;
            padding: 40px;
            width: calc(100% - 220px);
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin: auto;
        }
        .table-container {
            overflow-x: auto;
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead {
            background: #6a0dad;
            color: white;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        tbody tr:hover {
            background-color: #f0e6fa;
        }
        .no-data {
            text-align: center;
            padding: 15px;
            color: #888;
        }
        .feedback-btn {
            background: #673ab7;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
        .feedback-btn:hover {
            background: #512da8;
        }
    .feedback-container {
        position: absolute;
        top: 90px;
        right: 90px;
    }
    .feedback-modal {
        display: none;
        position: fixed;
        z-index: 1200;
        left: 60%;
        top: 40%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border-radius: 10px;
    }
    .feedback-modal textarea {
        width: 100%;
        height: 100px;
        margin-top: 10px;
    }
    .feedback-modal button {
        margin-top: 10px;
        color: #673ab7;
    }
</style>

<body>
    <div class="sidebar">
        <div class="profile-section">
            <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="profile-pic">
            <p><?php echo htmlspecialchars($firstname . " " . $lastname); ?></p>
        </div>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="SitinRules.php">Sit-in Rules</a></li>
            <li><a href="Labrules&Regulations.php">Lab Rules</a></li>
            <li><a href="announcements.php">Announcement</a></li>
            <li><a href="Reservation.php">Reservation</a></li>
            <li><a href="SitinHistory.php">History</a></li>
            <li><a href="ViewSession.php">Session</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="feedback-modal" id="feedbackModal">
    <h2>Submit Feedback</h2>
    <textarea id="feedbackText" placeholder="Write your feedback here..."></textarea>
    <button type="button" onclick="submitFeedback()">Submit</button>
    <button type="button" onclick="closeFeedbackModal()">Cancel</button>
   </div>
    <div class="main-content">
        <div class="container">
            <h1 class="title">Sit-in History</h1>
           <!-- Feedback Button -->
<div class="feedback-container">
    <button class="feedback-btn" onclick="openFeedbackModal()">Give Feedback</button>
</div>

<!-- Feedback Modal -->
<div id="feedbackModal" class="feedback-modal">
    <div class="feedback-content">
        <h2>Submit Feedback</h2>
        <textarea id="feedbackText" placeholder="Write your feedback here..."></textarea>
        <button type="button" onclick="submitFeedback()">Submit</button>
        <button type="button" onclick="closeFeedbackModal()">Cancel</button>
    </div>
</div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                            <th>ID No.</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Purpose</th>
                            <th>Laboratory</th>
                            <th>Sit-in Time</th>
                            <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($listPerson)) : ?>
                                <?php foreach ($listPerson as $person) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($person['idno']); ?></td>
                                        <td><?php echo htmlspecialchars($firstname . " " . $lastname); ?></td>
                                        <td><?php echo htmlspecialchars($person['purpose'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($person['TimeIn'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($person['Timeout'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($person['date'] ?? ''); ?></td>
                                        <td>
                                        <>
                                        <td>
                                        <?php if (!empty($person['action']) && $person['action'] === 'Submitted'): ?>
                                        <a href="feedback.php" class="feedback-btn">Submitted</a>
                                        <?php else: ?>
                                           Pending
                                        <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5" class="no-data">No history available</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <script>
        function openFeedbackModal() {
            let modal = document.getElementById("feedbackModal");
            modal.style.display = "block";
        }

        function closeFeedbackModal() {
            let modal = document.getElementById("feedbackModal");
            modal.style.display = "none";
        }

        function submitFeedback() {
            let feedback = document.getElementById("feedbackText").value;
            
            if (feedback.trim() === "") {
                alert("Feedback cannot be empty.");
                return;
            }

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "submit_feedback.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    if (xhr.responseText.trim() === "success") {
                        alert("Feedback successfully submitted");
                        closeFeedbackModal();
                        location.reload();
                    } else {
                        alert("Error submitting feedback.");
                    }
                }
            };
            xhr.send("feedback=" + encodeURIComponent(feedback) + "&idno=<?php echo $idno; ?>");
        }

        // Close modal if user clicks outside of it
        window.onclick = function(event) {
            let modal = document.getElementById("feedbackModal");
            if (event.target === modal) {
                closeFeedbackModal();
            }
        };
    </script>

                </div>
            </div>
        </div>
    </body>
    </html>
