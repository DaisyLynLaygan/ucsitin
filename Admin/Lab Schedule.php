<?php
session_start();
include './connection.php';
include 'navbar.php';

if (!isset($_SESSION['idno'])) {
    header("Location: login.php");
    exit;
}

$firstname = $_SESSION['firstname'] ?? '';
$lastname = $_SESSION['lastname'] ?? '';
$profile_picture = $_SESSION['profile_picture'] ?? 'de.jpg';

// Static slots and labs
$timeSlots = [
    "7:30AM–9:00AM", "9:00AM–10:30AM", "10:30AM–12:00PM",
    "12:00PM–1:30PM", "1:30PM–3:00PM", "3:00PM–4:30PM",
    "4:30PM–6:00PM", "6:00PM–7:30PM", "7:30PM–9:00PM"
];

$labs = ["Lab 517", "Lab 524", "Lab 526", "Lab 528", "Lab 530", "Lab 542", "Lab 544"];
$days = ["Monday/Wednesday", "Tuesday/Thursday", "Friday", "Saturday"];

// Simulated availability data (replace with DB logic if needed)
$schedule = [];
foreach ($days as $day) {
    foreach ($timeSlots as $slot) {
        foreach ($labs as $lab) {
            $status = (rand(0, 1) === 0) ? "Available" : "Occupied";
            $schedule[$day][$slot][$lab] = $status;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab Schedule</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: whitesmoke;
            color: #6a0dad;
            display: flex;
        }
        .main-content {
            margin-left: 160px;
            padding: 40px;
            width: calc(100% - 260px);
        }

        h2 {
            color: #6a0dad;
            margin-bottom: 10px;
        }

        .tabs {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .tab-button {
            background: none;
            border: none;
            color: #6a0dad;
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            font-size: 16px;
        }
        .tab-button.active {
            border-color: #6a0dad;
            color: #6a0dad;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: whitesmoke;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: whitesmoke;
            color: whitesmoke;
        }
        .available {
            background-color: #16a34a;
            color: white;
            padding: 8px 16px;
            border-radius: 64px;
        }
        .occupied {
            background-color: #dc2626;
            color: white;
            padding: 8px 16px;
            border-radius: 64px;
        }
        .legend {
            margin-top: 20px;
            background-color: #6a0dad;
            padding: 15px;
            border-radius: 10px;
            font-size: 14px;
            color: #cbd5e1;
        }
    </style>
    <script>
        function switchTab(tabName) {
            const tables = document.querySelectorAll(".schedule-table");
            tables.forEach(t => t.style.display = "none");
            document.getElementById(tabName).style.display = "block";

            const buttons = document.querySelectorAll(".tab-button");
            buttons.forEach(btn => btn.classList.remove("active"));
            document.getElementById("btn-" + tabName).classList.add("active");
        }

        window.onload = () => {
            switchTab("Monday/Wednesday");
        };
    </script>
</head>
<body>
    <div class="main-content">
        <h2>Lab Schedule - <span id="day-label">Monday/Wednesday</span></h2>
        <div class="tabs">
            <?php foreach ($days as $day): ?>
                <button class="tab-button" id="btn-<?php echo $day; ?>" onclick="switchTab('<?php echo $day; ?>')">
                    <?php echo $day; ?>
                </button>
            <?php endforeach; ?>
        </div>

        <?php foreach ($days as $day): ?>
            <div id="<?php echo $day; ?>" class="schedule-table" style="display: none;">
                <table>
                    <thead>
                        <tr>
                            <th>Time Slot</th>
                            <?php foreach ($labs as $lab): ?>
                                <th><?php echo $lab; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($timeSlots as $slot): ?>
                            <tr>
                                <td><?php echo $slot; ?></td>
                                <?php foreach ($labs as $lab): ?>
                                    <?php $status = $schedule[$day][$slot][$lab]; ?>
                                    <td class="<?php echo strtolower($status); ?>">
                                        <?php echo $status; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>

        <div class="legend">
            <p><span class="available">Available</span> = Lab is vacant during this time</p>
            <p><span class="occupied">Occupied</span> = Lab is in use during this time</p>
        </div>
    </div>
</body>
</html>



