<?php
include './connection.php';

<<<<<<< HEAD
// Initialize error messages
$errors = [];

// Fetch courses from the database
$course_query = "SELECT * FROM courses"; 
$course_result = $conn->query($course_query);
$courses = [];

if ($course_result->num_rows > 0) {
    while ($row = $course_result->fetch_assoc()) {
        $courses[] = $row;
    }
}

// Initialize empty array for years
$years = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and collect form inputs
    $idno = $_POST['idno'];
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'] ? $_POST['middlename'] : '';  // Added Middlename field
=======
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and collect the form inputs
    $idno = $_POST['idno'];
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
    $course = $_POST['course'];
    $year = $_POST['year'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
<<<<<<< HEAD
    $repeat_password = $_POST['repeat-password'];

    // Validation logic
    if (empty($idno)) {
        $errors['idno'] = "Student ID is required.";
    }

    if (empty($lastname)) {
        $errors['lastname'] = "Last Name is required.";
    }

    if (empty($firstname)) {
        $errors['firstname'] = "First Name is required.";
    }

    if (empty($course)) {
        $errors['course'] = "Course selection is required.";
    }

    if (empty($year)) {
        $errors['year'] = "Year selection is required.";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required.";
    }

    if (empty($username)) {
        $errors['username'] = "Username is required.";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required.";
    }

    if ($password != $repeat_password) {
        $errors['password_match'] = "Passwords do not match.";
    }

    // Check if email or name combination already exists
    if (count($errors) == 0) {
        // Check if student with the same name or email already exists
        $check_query = "SELECT * FROM student WHERE (firstname = ? AND middlename = ? AND lastname = ?) OR (firstname = ? AND lastname = ?) OR email = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("ssssss", $firstname, $middlename, $lastname, $firstname, $lastname, $email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ($row['email'] == $email) {
                    $errors['email'] = "The email already exists. Try another one.";
                } 
                if ($row['firstname'] == $firstname && $row['lastname'] == $lastname) {
                    $errors['duplicate'] = "A student with the same name already exists.";
                }
                if ($row['firstname'] == $firstname && $row['middlename'] == $middlename && $row['lastname'] == $lastname) {
                    $errors['duplicate'] = "A student with the same name already exists.";
                }
            }
        }
    
        // Check if the ID number or username already exists
        $check_id_query = "SELECT idno, username FROM student WHERE idno = ? OR username = ?";
        $stmt = $conn->prepare($check_id_query);
        $stmt->bind_param("ss", $idno, $username);
        $stmt->execute();
        $result = $stmt->get_result();
    
        while ($row = $result->fetch_assoc()) {
            if ($row['idno'] == $idno) {
                $errors['idno'] = "The ID number already exists. Try another one.";
            }
            if ($row['username'] == $username) {
                $errors['username'] = "The Username already exists. Try another one.";
            }
        }
    }     

    // Only proceed if there are no errors
    if (count($errors) == 0) {
        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Determine session number based on course like if the course is BSIT or BSCS then the session number is 30
        // Otherwise, if other course then it is 15
        $student_session_no = ($course == 'BSIT' || $course == 'BSCS') ? 30 : 15;

        // Prepare an SQL query to insert the data
        $stmt = $conn->prepare("INSERT INTO student (idno, lastname, firstname, middlename, course, year, email, username, password, session_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssisssi", $idno, $lastname, $firstname, $middlename, $course, $year, $email, $username, $hashed_password, $student_session_no);

        // Execute the query and check if it was successful
        if ($stmt->execute()) {
            echo "<script>alert('Registered Successfully.');</script>";
            header("Location: login.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
    }
}

// Fetch years from the database based on the selected course
if (isset($_POST['course'])) {
    $selected_course = $_POST['course'];
    // Get the number of years for the selected course
    $course_query = "SELECT year FROM courses WHERE name = ?";
    $stmt = $conn->prepare($course_query);
    $stmt->bind_param("s", $selected_course);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_years = $row['year'];

        // Generate an array of years based on the course year value
        for ($i = 1; $i <= $total_years; $i++) {
            $years[] = $i;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 90vh;
            margin-top: 20px;
        }
        .container {
            background: whitesmoke;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        h2 {
            text-align: center;
            color: #6A0DAD;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 10px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .register-btn {
            width: 100%;
            padding: 10px;
            background-color: #6A0DAD;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .error {
            color: red;
            font-size: 12px;
        }
        .error-messages {
            color: red;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .form-row {
            display: flex;
            gap: 30px;
        }

        .form-group.half {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registration</h2>
        
        <?php if (isset($errors['duplicate'])): ?>
            <div class="error"><?= $errors['duplicate']; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="idno">Student ID Number:</label>
                <input type="text" id="idno" name="idno" value="<?= isset($idno) ? $idno : ''; ?>">
                <?php if (isset($errors['idno'])): ?>
                    <div class="error" style="padding: 2px"><?= $errors['idno']; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group half">
                    <label for="firstname">First Name:</label>
                    <input type="text" id="firstname" name="firstname" value="<?= isset($firstname) ? $firstname : ''; ?>">
                    <?php if (isset($errors['firstname'])): ?>
                        <div class="error"><?= $errors['firstname']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group half">
                    <label for="middlename">Middle Name:</label>
                    <input type="text" id="middlename" name="middlename" value="<?= isset($middlename) ? $middlename : ''; ?>">
                    <?php if (isset($errors['middlename'])): ?>
                        <div class="error"><?= $errors['middlename']; ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="lastname">Last Name:</label>
                <input type="text" id="lastname" name="lastname" value="<?= isset($lastname) ? $lastname : ''; ?>">
                <?php if (isset($errors['lastname'])): ?>
                    <div class="error"><?= $errors['lastname']; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group half">
                    <label for="course">Course:</label>
                    <select id="course" name="course" onchange="this.form.submit()">
                        <option value="">Select Course</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= $course['name']; ?>" <?= isset($selected_course) && $selected_course == $course['name'] ? 'selected' : ''; ?>>
                                <?= $course['description']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['course'])): ?>
                        <div class="error"><?= $errors['course']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group half">
                    <label for="year">Year Level:</label>
                    <select id="year" name="year">
                        <option value="">Select Year Level</option>
                        <?php foreach ($years as $year): ?>
                            <option value="<?= $year; ?>" <?= isset($year) && $year == $year ? 'selected' : ''; ?>>
                                <?= $year == 1 ? $year . 'st Year' : ($year == 2 ? $year . 'nd Year' : ($year == 3 ? $year . 'rd Year' : $year . 'th Year')); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['year'])): ?>
                        <div class="error"><?= $errors['year']; ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= isset($email) ? $email : ''; ?>">
                <?php if (isset($errors['email'])): ?>
                    <div class="error"><?= $errors['email']; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?= isset($username) ? $username : ''; ?>">
                <?php if (isset($errors['username'])): ?>
                    <div class="error"><?= $errors['username']; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group half" style="position: relative;">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password">
                    <i class="fa-solid fa-eye toggle-password" data-target="password" style="position: absolute; right: 0; top: 36px; cursor: pointer;"></i>
                    <?php if (isset($errors['password'])): ?>
                        <div class="error"><?= $errors['password']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group half" style="position: relative;">
                    <label for="repeat-password">Repeat Password:</label>
                    <input type="password" id="repeat-password" name="repeat-password">
                    <i class="fa-solid fa-eye toggle-password" data-target="repeat-password" style="position: absolute; right: 0; top: 36px; cursor: pointer;"></i>
                    <?php if (isset($errors['password_match'])): ?>
                        <div class="error"><?= $errors['password_match']; ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="register-btn">Register</button>
        </form>

        <p style="text-align: center; margin-top: 20px;">
            Already have an account? <a href="login.php" style="color: #6A0DAD;">Login here</a>
        </p>
    </div>

    <script>
        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', function () {
                const targetInput = document.getElementById(this.dataset.target);
                const type = targetInput.getAttribute('type') === 'password' ? 'text' : 'password';
                targetInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        });
    </script>
=======

    // Hash the password before storing it
    $hashed_password = $password;

    // Prepare an SQL query to insert the data
    $stmt = $conn->prepare("INSERT INTO student (idno, lastname, firstname, middlename, course, year, email, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssisss", $idno, $lastname, $firstname, $middlename, $course, $year, $email, $username, $hashed_password);

    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://www.phptutorial.net/app/css/style.css">
<title>Register</title>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }
    body {
        overflow: hidden;
        display: flex;
        height: 100vh;
        justify-content: center;
        align-items: center;
        background-color: whitesmoke;
    }
    .container {
        display: flex;
        width: 90%;
        max-width: 900px;
        height: 90vh;
        background: white;
        border-radius: 10px;
        box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    }
    .left {
        flex: 1;
        background-image: url("OP.jpg");
        background-size: cover;
        background-position: center;
    }
    .right {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: whitesmoke;
        padding: 20px;
    }
    .form-container {
        width: 100%;
        max-width: 450px;
        display: flex;
        flex-direction: column;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.1);
    }
    .scroll-container {
        max-height: 50vh;
        overflow-y: auto;
        padding-right: 5px;
    }
    .scroll-container::-webkit-scrollbar {
        width: 8px;
    }
    .scroll-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 5px;
    }
    .scroll-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 5px;
    }
    .form-container img {
        width: 30%;
        height: auto;
        margin-bottom: 15px;
    }
    .form-container h1 {
        margin-bottom: 15px;
        color: #6A0DAD; /* Purple */
    }
    .input-group {
        margin-bottom: 10px;
        text-align: left;
    }
    .input-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    .input-group input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    .btn-container {
        display: flex;
        justify-content: space-between;
        padding-top: 10px;
        position: sticky;
        bottom: 0;
        background: white;
        padding-bottom: 10px;
    }
    .btn {
        flex: 1;
        padding: 10px;
        border: none;
        color: white;
        cursor: pointer;
        border-radius: 5px;
        font-size: 16px;
        margin: 0 5px;
    }
    .btn-register {
        color: #6A0DAD;
    }
    @media (max-width: 687px) {
        .container {
            flex-direction: column;
            height: auto;
        }
        .left {
            display: none; /* Hide image on small screens */
        }
        .form-container {
            width: 100%;
        }
    }
</style>
</head>
<body>
<div class="container">
    <div class="left"></div>
    <div class="right">
        <div class="form-container">
            <form method="POST">
                <center>
                    <img src="../sitin/CCS LOGO.png" width="30%" height="auto"/>
                </center>
               <center><h2><b>Sign Up!</b></h2></center>
                <div class="scroll-container">
                    <div class="input-group">
                        <label for="idno">ID No:</label>
                        <input type="number" name="idno" id="idno">
                    </div>
                    <div class="input-group">
                        <label for="lastname">Last Name:</label>
                        <input type="text" name="lastname" id="lastname">
                    </div>
                    <div class="input-group">
                        <label for="firstname">First Name:</label>
                        <input type="text" name="firstname" id="firstname">
                    </div>
                    <div class="input-group">
                        <label for="middlename">Middle Name:</label>
                        <input type="text" name="middlename" id="middlename">
                    </div>
                    <div class="input-group">
                        <label for="course">Course:</label>
                        <input type="text" name="course" id="course">
                    </div>
                    <div class="input-group">
                        <label for="year">Year Level:</label>
                        <input type="number" name="year" id="year">
                    </div>
                    <div class="input-group">
                        <label for="email">Email Address:</label>
                        <input type="email" name="email" id="email">
                    </div>
                    <div class="input-group">
                        <label for="username">Username:</label>
                        <input type="text" name="username" id="username">
                    </div>
                    <div class="input-group">
                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password">
                    </div>
                </div>
                <div class="btn-container">
                    <button type="submit" class="btn btn-signin">Sign In</button>
                    <a href="login.php" class="btn btn-register"><center>Login</center></a>
                </div>
            </form>
        </div>
    </div>
</div>
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
</body>
</html>
