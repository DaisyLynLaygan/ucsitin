<?php
session_start();
include 'connection.php';
include 'navbar.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Get selected lab
$selectedLab = $_GET['lab'] ?? 'Lab 524';

// Fetch labs
$labs = ['Lab 524', 'Lab 526', 'Lab 528', 'Lab 530', 'Lab 542', 'Lab 544'];

// Fetch PC statuses
$pc_query = $conn->prepare("SELECT * FROM pcs WHERE lab = ?");
$pc_query->bind_param("s", $selectedLab);
$pc_query->execute();
$pc_result = $pc_query->get_result();

$pcs = [];
$total = 0;
$available = 0;
$used = 0;
$maintenance = 0;

while ($row = $pc_result->fetch_assoc()) {
    $pcs[] = $row;
    $total++;
    if ($row['status'] === 'Available') $available++;
    elseif ($row['status'] === 'Used') $used++;
    elseif ($row['status'] === 'Maintenance') $maintenance++;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab Management</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #fafafa;
            color: white;
        }
        .main-content {
        /*    margin-right: 120px; */
            padding: 20px;
            align-items: center;
            width: 90%;

        }
        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 600;
            color: #6a0dad;
        }
        .tabs {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            border-bottom: 2px solid #6a0dad;
            padding-bottom: 10px;
        }
        .tabs a {
            text-decoration: none;
            color: #6a0dad;
            padding: 8px 12px;
        }
        .tabs a.active {
            color: #6a0dad;
            border-bottom: 3px solid #6a0dad;
        }
        .summary {
            margin: 10px 0 20px;
            font-size: 14px;
            color: #fafafa;
        }
        .status-circle {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
        }
        .green { background-color: #22c55e; }
        .red { background-color: #ef4444; }
        .yellow { background-color: #eab308; }

        .lab-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 12px;
            color: #6a0dad;
        }
        .update-bar {
            background: #6a0dad;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .update-bar select, .update-bar button {
            padding: 8px 10px;
            margin-left: 10px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .update-bar select {
            background-color: #6a0dad;
            color: #e2e8f0;
        }

        .update-bar button {
            background-color: #6a0dad;
            color: white;
            cursor: pointer;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
        }

        .pc-card {
            background-color: #6a0dad;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .pc-card i {
            font-size: 32px;
            margin-bottom: 10px;
            color: #6a0dad;
        }

        .pc-card .label {
            font-size: 14px;
            font-weight: bold;
            color: #6a0dad;
        }

        .available { color: #22c55e; }
        .used { color: #ef4444; }
        .maintenance { color: #eab308; }

            .grid-wrapper {
            display: flex;
            justify-content: center;
                    margin-top: 20px;
                }
            .grid {
                width: 100%;
                max-width: 1000px;
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                gap: 15px;
            }

        .pc-card {
            position: relative;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 10px;
            text-align: center;
            transition: 0.3s;
            box-shadow: 0 0 6px rgba(0,0,0,0.1);
        }
        .pc-card:hover {
            transform: translateY(-3px);
        }
        .pc-checkbox {
            position: absolute;
            top: 10px;
            left: 10px;
        }
        .status-circle {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }
        .status-circle.green { background-color: #22c55e; }
        .status-circle.red { background-color: #ef4444; }
        .status-circle.yellow { background-color: #facc15; }

        .action-button {
            padding: 6px 14px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            color: white;
            cursor: pointer;
        }
        .action-button.green { background-color:rgb(3, 176, 66); }
        .action-button.red { background-color: #ef4444; }
        .action-button.yellow { background-color:rgb(244, 195, 0); color: black; }
        </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="main-content">
        <h2>Computer Lab Management</h2>

        <div class="tabs">
            <?php foreach ($labs as $lab): ?>
                <a class="<?= $lab === $selectedLab ? 'active' : '' ?>" href="?lab=<?= urlencode($lab) ?>"><?= $lab ?></a>
            <?php endforeach; ?>
        </div>

        <div class="lab-title"><?= $selectedLab ?></div>

        <div class="update-bar" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <button id="edit-toggle" style="padding: 6px 12px; background-color: #0d6efd; color: white; border: none; border-radius: 5px; cursor: pointer;">
                🛠️ Edit PCs
            </button>

            <div class="summary" style="display: flex; align-items: center; gap: 20px; font-size: 14px;">
                <span><span class="status-circle green"></span> Available: <?= $available ?></span>
                <span><span class="status-circle red"></span> In Use: <?= $used ?></span>
                <span><span class="status-circle yellow"></span> Maintenance: <?= $maintenance ?></span>
                <span>Total PCs: <?= $total ?></span>
            </div>
        </div>

       <!-- Buttons + hidden form -->
       <form id="bulk-update-form" method="POST" action="update_selected_pcs.php" style="display: none; gap: 10px; flex-wrap: wrap; margin-top: 20px;">
            <input type="hidden" name="lab" value="<?= $selectedLab ?>">
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <button name="mark" value="Available" type="submit" class="action-button green">Mark Available</button>
                <button name="mark" value="Used" type="submit" class="action-button red">Mark Used</button>
                <button name="mark" value="Maintenance" type="submit" class="action-button yellow">Mark Maintenance</button>
                <span id="toggle-select" style="color: #007bff; cursor: pointer; font-weight: bold;">✔ Select All</span>
                <span id="cancel-edit" style="color: #ff5e5e; cursor: pointer; font-weight: bold;">❌ Cancel</span>
            </div>
        </form>

        <!-- Always visible PC cards -->
        <div class="grid-wrapper" style="width: 100%;">
            <div class="grid">
                <?php foreach ($pcs as $pc): ?>
                    <div class="pc-card <?= strtolower($pc['status']) ?>">
                        <input type="checkbox" class="pc-checkbox" data-id="<?= htmlspecialchars($pc['id']) ?>" style="display:none;">
                        <i class="fas fa-desktop"></i>
                        <div class="label"><?= htmlspecialchars($pc['pc_no']) ?></div>
                        <div style="font-weight: 700;" class="<?= strtolower($pc['status']) ?>"><?= $pc['status'] ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        const editBtn = document.getElementById("edit-toggle");
        const cancelBtn = document.getElementById("cancel-edit");
        const form = document.getElementById("bulk-update-form");
        const toggleSelectBtn = document.getElementById("toggle-select");
        const pcCheckboxes = document.querySelectorAll(".pc-checkbox");

        editBtn.addEventListener("click", () => {
            form.style.display = "flex";
            document.querySelectorAll(".pc-checkbox").forEach(cb => cb.style.display = "block");
        });

        cancelBtn.addEventListener("click", () => {
            form.style.display = "none";
            document.querySelectorAll(".pc-checkbox").forEach(cb => {
                cb.style.display = "none";
                cb.checked = false;
            });
        });

        // Function to update toggle text based on current checkbox state
        function updateToggleText() {
            const allChecked = [...pcCheckboxes].every(cb => cb.checked);
            toggleSelectBtn.textContent = allChecked ? "🔄 Unselect All" : "✔ Select All";
        }

        // Toggle Select/Unselect All
        toggleSelectBtn.addEventListener("click", function () {
            const allChecked = [...pcCheckboxes].every(cb => cb.checked);
            pcCheckboxes.forEach(cb => cb.checked = !allChecked);
            updateToggleText();
        });

        // Update label on individual checkbox change
        pcCheckboxes.forEach(cb => {
            cb.addEventListener("change", updateToggleText);
        });

        // Edit Mode On
        document.getElementById("edit-toggle").addEventListener("click", function () {
            document.getElementById("bulk-update-form").style.display = "flex";
            pcCheckboxes.forEach(cb => cb.style.display = "block");
            updateToggleText();
        });

        // Cancel Edit
        document.getElementById("cancel-edit").addEventListener("click", function () {
            document.getElementById("bulk-update-form").style.display = "none";
            pcCheckboxes.forEach(cb => {
                cb.style.display = "none";
                cb.checked = false;
            });
            updateToggleText();
        });

        // Before submitting, move checked checkboxes to the form
        form.addEventListener("submit", (e) => {
            // Remove any previous hidden checkboxes from form
            form.querySelectorAll("input[name='pcs[]']").forEach(el => el.remove());

            document.querySelectorAll(".pc-checkbox").forEach(cb => {
                if (cb.checked) {
                    const hiddenInput = document.createElement("input");
                    hiddenInput.type = "hidden";
                    hiddenInput.name = "pcs[]";
                    hiddenInput.value = cb.dataset.id;
                    form.appendChild(hiddenInput);
                }
            });
        });
    </script>
</body>
</html>
