<?php
include './connection.php'; // Database connection
include 'navbar.php';

$student = null;
$message = "";

// Handle search
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $idno = $conn->real_escape_string($_POST['idno']);
    
    // Fetch student details
    $query = "SELECT idno, firstname, lastname FROM student WHERE idno = '$idno'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        $message = "Student not found!";
    }
}

// Handle sit-in record submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sit_in'])) {
    $idno = $conn->real_escape_string($_POST['idno']);
    $purpose = $conn->real_escape_string($_POST['purpose']);
    $laboratory = $conn->real_escape_string($_POST['laboratory']);

    // Insert sit-in record
    $query = "INSERT INTO sit_in (idno, purpose, laboratory) VALUES ('$idno', '$purpose', '$laboratory')";

    if ($conn->query($query) === TRUE) {
        $message = "Sit-in record saved successfully!";
        $student = null; // Clear student info after successful submission
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Student & Sit-in</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
        }
        .container {
            width: 60%;
            margin: 40px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        input, button {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: purple;
            color: white;
            cursor: pointer;
            border: none;
        }
        button:hover {
            background-color: #5a005a;
        }
        .message {
            color: red;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: purple;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Search Student</h2>
        <form method="POST">
            <input type="text" name="idno" placeholder="Enter Student ID" required>
            <button type="submit" name="search">Search</button>
        </form>

        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <?php if ($student): ?>
            <h3>Student Information</h3>
            <table>
                <tr>
                    <th>ID No.</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                </tr>
                <tr>
                    <td><?php echo $student['idno']; ?></td>
                    <td><?php echo $student['firstname']; ?></td>
                    <td><?php echo $student['lastname']; ?></td>
                </tr>
            </table>

            <h3>Sit-in Form</h3>
            <form method="POST">
                <input type="hidden" name="idno" value="<?php echo $student['idno']; ?>">

                <label>Purpose:</label>
                <input type="text" name="purpose" required>

                <label>Laboratory:</label>
                <input type="text" name="laboratory" required>

                <button type="submit" name="sit_in">Sit-in</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
