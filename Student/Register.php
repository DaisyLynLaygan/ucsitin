<?php
include './connection.php';

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
    $course = $_POST['course'];
    $year = $_POST['year'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
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
</body>
</html>
