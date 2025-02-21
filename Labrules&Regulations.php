<?php
session_start();

// Dummy data for lab rules (ideally, fetch from a database)
$labRules = [
    "No food or drinks allowed inside the lab.",
    "Always log in and log out when using lab computers.",
    "Do not install or uninstall any software without permission.",
    "Maintain silence and avoid disturbing others.",
    "Report any hardware or software issues to the lab administrator.",
    "Use the lab resources responsibly and ethically."
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Rules & Regulations</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: whitesmoke;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: purple;
            text-align: center;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            background: #f4f4f4;
            margin: 10px 0;
            padding: 10px;
            border-left: 5px solid purple;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Lab Rules & Regulations</h1>
        <ul>
            <?php foreach ($labRules as $rule): ?>
                <li><?php echo htmlspecialchars($rule); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
