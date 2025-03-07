<?php
session_start(); // Start the session
include './connection.php';

// Ensure session variables are set
if (!isset($_SESSION['idno'])) {
    header("Location: login.php");
    exit;
}

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
$conn = new mysqli('localhost', 'root', '', 'sitin');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sit-in Monitoring Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            background-color: whitesmoke;
            margin: 0;
        }
        .sidebar {
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

        .profile-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid white;
            cursor: pointer;
        }
        .hidden-input {
            display: none;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            width: 100%;
        }
        .sidebar ul li {
            padding: 15px;
            text-align: center;
            transition: background 0.3s;
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

    </style>
</head>
<body>
    <div class="sidebar">
        <div class="profile-section">
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
</body>
</html>
