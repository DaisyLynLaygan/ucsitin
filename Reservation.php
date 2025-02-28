<?php
require_once '../asset/navbar_connection.php';

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Establish database connection
$con = mysqli_connect('localhost', 'root', '', 'ccs_system');
if ($con === false) {
    die("Error: Could not connect to the database. " . mysqli_connect_error());
}

// Fetch user profile information
$userProfile = [];
$queryProfile = "SELECT firstname, lastname, profile_picture FROM users WHERE username = ?";
if ($stmt = mysqli_prepare($con, $queryProfile)) {
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['username']);
    mysqli_stmt_execute($stmt);
    $resultProfile = mysqli_stmt_get_result($stmt);
    $userProfile = mysqli_fetch_assoc($resultProfile);
    mysqli_stmt_close($stmt);
}

if (isset($_POST["submitReserve"])) {
    $programming = $_POST["purpose"] ?? '';
    $selected_lab = $_POST["lab"] ?? '';
    
    if (!empty($selected_lab)) {
        $lab = mysqli_real_escape_string($con, $selected_lab);
        $sentence = "lab_" . $lab;
        $sqlTable = "SELECT pc_id FROM student_pc WHERE `$sentence` = '1'";
        $result = mysqli_query($con, $sqlTable);
    } else {
        $result = false;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation</title>
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            background-color: whitesmoke;
            margin: 0;
            overflow: hidden;
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
            padding: 40px;
            width: calc(100% - 270px);
            margin-left: 270px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="profile-section">
            <img src="<?php echo htmlspecialchars($userProfile['profile_picture'] ?? 'de.jpg'); ?>" alt="Profile Picture" class="profile-pic">
            <p><?php echo htmlspecialchars($userProfile['firstname'] . " " . $userProfile['lastname']); ?></p>
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
    <div class="main-content">
        <h4 class="text-center">Reservation</h4>
        <form action="Reservation.php" method="POST">
            <div>
                <label for="id">ID Number:</label>
                <input id="id" name="id_number" type="text" value="<?php echo $_SESSION['id_number'] ?>" readonly />
            </div>
            <div>
                <label for="name">Student Name:</label>
                <input id="name" name="studentName" type="text" value="<?php echo $_SESSION['name'] ?>" readonly />
            </div>
            <div>
                <label for="purposes">Purpose:</label>
                <select name="purpose" id="purposes" required>
                    <option value="C Programming" <?php if($programming == "C Programming") echo 'selected'; ?>>C Programming</option>
                    <option value="Java Programming" <?php if($programming == "Java Programming") echo 'selected'; ?>>Java Programming</option>
                </select>
            </div>
            <div>
                <label for="lab">Lab:</label>
                <select name="lab" id="lab" required>
                    <option value="524" <?php if($selected_lab == "524") echo 'selected'; ?>>524</option>
                    <option value="526" <?php if($selected_lab == "526") echo 'selected'; ?>>526</option>
                </select>
                <button type="submit" name="submitReserve">Submit</button>
            </div>
        </form>
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <label>Available PC:</label>
            <select name="pc_number">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <option value="<?php echo $row['pc_id']; ?>"><?php echo $row['pc_id']; ?></option>
                <?php endwhile; ?>
            </select>
        <?php endif; ?>
    </div>
</body>
</html>
