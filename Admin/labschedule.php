<?php
// This must be the VERY FIRST LINE - no whitespace before!
session_start();
$pageTitle = "Lab Schedule Management";

// Include database connection BEFORE any potential header redirects
include '../student/connection.php';

// Handle schedule status updates before any output
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['selected_days']) && isset($_POST['laboratory']) && isset($_POST['action'])) {
        $selected_days = $_POST['selected_days'];
        $laboratory = $_POST['laboratory'];
        $action = $_POST['action'];
        
        // Validate action
        $valid_actions = ['available', 'unavailable', 'reserved'];
        if (!in_array($action, $valid_actions)) {
            die("Invalid action");
        }
        
        // Update each selected day
        foreach ($selected_days as $day) {
            // Validate day
            $valid_days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            if (!in_array($day, $valid_days)) {
                continue;
            }
            
            $stmt = $conn->prepare("INSERT INTO lab_schedules (laboratory, day, status, created_at, updated_at) 
                                  VALUES (?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                                  ON DUPLICATE KEY UPDATE status = ?, updated_at = CURRENT_TIMESTAMP");
            $stmt->bind_param("ssss", $laboratory, $day, $action, $action);
            $stmt->execute();
            $stmt->close();
        }
        
        $_SESSION['success_message'] = 'Lab schedule updated successfully!';
        header("Location: labschedule.php?laboratory=".urlencode($laboratory));
        exit();
    }
}

// Now include header after all potential header operations are complete
include 'header.php';

// Display success message if set
if (isset($_SESSION['success_message'])) {
    echo "<script>alert('".addslashes($_SESSION['success_message'])."');</script>";
    unset($_SESSION['success_message']);
}

// Get current laboratory from query parameter (fixed typo in variable name)
$current_laboratory = isset($_GET['laboratory']) ? $_GET['laboratory'] : '524';

// Fetch all schedule data for the current laboratory
$schedule_data = [];
$stmt = $conn->prepare("SELECT day, status FROM lab_schedules WHERE laboratory = ?");
$stmt->bind_param("s", $current_laboratory);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $schedule_data[$row['day']] = $row['status'];
}
$stmt->close();
?>
<style>
.container {
    max-width: 1400px;
    margin: 20px auto;
    padding: 0 20px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e0e0e0;
}

.page-title {
    font-size: 20px;
    font-weight: 600;
    color: #3f51b5;
    margin: 0;
}

.nav-btn {
    padding: 8px 15px;
    border-radius: 4px;
    font-size: 14px;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #3f51b5;
    color: #fff;
    border: none;
    font-weight: 500;
}

.nav-btn i {
    font-size: 14px;
}

.laboratory-selector {
    margin-bottom: 25px;
    background: white;
    padding: 15px;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.laboratory-selector-title {
    font-size: 15px;
    font-weight: 500;
    color: #3f51b5;
    margin-bottom: 12px;
}

.laboratory-btn-group {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.laboratory-btn {
    padding: 8px 15px;
    border-radius: 4px;
    font-size: 13px;
    transition: all 0.2s;
    border: 1px solid #ddd;
    background: white;
    color: #3f51b5;
    font-weight: 500;
}

.laboratory-btn:hover {
    background-color: #f6f7fb;
    border-color: #3f51b5;
    color: #3f51b5;
}

.laboratory-btn.active {
    background-color: #3f51b5;
    border-color: #3f51b5;
    color: white;
}

.schedule-management-container {
    background: white;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 20px;
}

.schedule-management-header {
    padding: 15px 20px;
    background-color: #f8f9fa;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.schedule-management-title {
    font-size: 15px;
    font-weight: 500;
    color: #3f51b5;
    margin: 0;
}

.action-buttons {
    display: flex;
    gap: 10px;
}

.action-btn {
    padding: 6px 12px;
    font-size: 13px;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s;
    border: 1px solid #3f51b5;
    background: white;
    color: #3f51b5;
    font-weight: 500;
}

.action-btn.btn-primary,
.action-btn.btn-success,
.action-btn.btn-warning,
.action-btn.btn-danger {
    background: #3f51b5;
    color: #fff;
    border: 1px solid #3f51b5;
}

.action-btn:hover {
    background: #303f9f;
    color: #fff;
    border-color: #303f9f;
}

.action-btn i {
    font-size: 12px;
}

.days-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    padding: 20px;
}

.day-card {
    padding: 20px;
    border-radius: 6px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    position: relative;
    overflow: hidden;
    border: 1px solid transparent;
    min-height: 100px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.day-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 3px 6px rgba(0,0,0,0.1);
}

.day-card.available {
    background-color: rgba(40, 167, 69, 0.1);
    border-color: rgba(40, 167, 69, 0.2);
}

.day-card.unavailable {
    background-color: rgba(220, 53, 69, 0.1);
    border-color: rgba(220, 53, 69, 0.2);
}

.day-card.reserved {
    background-color: rgba(255, 193, 7, 0.1);
    border-color: rgba(255, 193, 7, 0.2);
}

.day-card.selected {
    border: 2px solid #3f51b5;
    box-shadow: 0 0 0 3px rgba(63,81,181, 0.15);
}

.day-name {
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 8px;
    color: #2c3e50;
}

.day-status {
    font-size: 13px;
    padding: 4px 8px;
    border-radius: 4px;
    display: inline-block;
    font-weight: 500;
}

.status-available {
    background-color: #28a745;
    color: white;
}

.status-unavailable {
    background-color: #dc3545;
    color: white;
}

.status-reserved {
    background-color: #ffc107;
    color: #212529;
}

.day-checkbox {
    position: absolute;
    opacity: 0;
}

/* Status indicator dot */
.status-indicator {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.available .status-indicator {
    background-color: #303f9f;
}

.unavailable .status-indicator {
    background-color: #303f9f;
}

.reserved .status-indicator {
    background-color: #303f9f;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .days-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 10px;
        padding: 15px;
    }
    
    .day-card {
        padding: 15px;
        min-height: 80px;
    }
}

@media (max-width: 576px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .days-grid {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    }
    
    .day-name {
        font-size: 14px;
    }
    
    .day-status {
        font-size: 12px;
    }
}

.container {
    max-width: 1400px;
    margin: 20px auto;
    padding: 0 20px;
}

/* ... (keep all your existing CSS styles) ... */
</style>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Lab Schedule Management</h1>
        <div>
            <a href="computer_lab_management.php" class="nav-btn btn-primary">
                <i class="fas fa-desktop"></i> Computer Lab
            </a>
        </div>
    </div>
    
    <!-- Laboratory Selection -->
    <div class="laboratory-selector">
        <p class="laboratory-selector-title">Select Laboratory:</p>
        <div class="laboratory-btn-group">
            <a href="?laboratory=517" class="laboratory-btn <?= $current_laboratory == '517' ? 'active' : '' ?>">517</a>
            <a href="?laboratory=524" class="laboratory-btn <?= $current_laboratory == '524' ? 'active' : '' ?>">524</a>
            <a href="?laboratory=526" class="laboratory-btn <?= $current_laboratory == '526' ? 'active' : '' ?>">526</a>
            <a href="?laboratory=528" class="laboratory-btn <?= $current_laboratory == '528' ? 'active' : '' ?>">528</a>
            <a href="?laboratory=530" class="laboratory-btn <?= $current_laboratory == '530' ? 'active' : '' ?>">530</a>
            <a href="?laboratory=542" class="laboratory-btn <?= $current_laboratory == '542' ? 'active' : '' ?>">542</a>
            <a href="?laboratory=544" class="laboratory-btn <?= $current_laboratory == '544' ? 'active' : '' ?>">544</a>
        </div>
    </div>
    
    <form method="POST" id="scheduleManagementForm">
        <input type="hidden" name="laboratory" value="<?= htmlspecialchars($current_laboratory) ?>">
        
        <div class="schedule-management-container">
            <div class="schedule-management-header">
                <h2 class="schedule-management-title">Lab Schedule: Room <?= htmlspecialchars($current_laboratory) ?></h2>
                <div class="action-buttons">
                    <button type="button" class="action-btn btn-outline-secondary" id="selectAllBtn">
                        <i class="fas fa-check-square"></i> Select All
                    </button>
                    <button type="submit" class="action-btn btn-success" name="action" value="available">
                        <i class="fas fa-check-circle"></i> Available
                    </button>
                    <button type="submit" class="action-btn btn-warning" name="action" value="reserved">
                        <i class="fas fa-calendar-check"></i> Reserved
                    </button>
                    <button type="submit" class="action-btn btn-danger" name="action" value="unavailable">
                        <i class="fas fa-times-circle"></i> Unavailable
                    </button>
                </div>
            </div>
            
            <!-- Days Grid -->
            <div class="days-grid">
                <?php
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                
                foreach ($days as $day):
                    // Get status from the fetched data or default to 'available'
                    $status = isset($schedule_data[$day]) ? $schedule_data[$day] : 'available';
                    
                    // Determine status class and display text
                    $status_class = '';
                    $status_text = '';
                    switch($status) {
                        case 'available':
                            $status_class = 'available';
                            $status_text = 'Available';
                            break;
                        case 'unavailable':
                            $status_class = 'unavailable';
                            $status_text = 'Unavailable';
                            break;
                        case 'reserved':
                            $status_class = 'reserved';
                            $status_text = 'Reserved';
                            break;
                        default:
                            $status_class = 'available';
                            $status_text = 'Available';
                    }
                ?>
                <div class="day-card <?= $status_class ?>" data-day="<?= $day ?>" data-status="<?= $status ?>">
                    <div class="status-indicator"></div>
                    <div class="day-name"><?= $day ?></div>
                    <div class="day-status status-<?= $status_class ?>">
                        <?= $status_text ?>
                    </div>
                    <input type="checkbox" name="selected_days[]" value="<?= $day ?>" class="day-checkbox">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle day selection on click
    const dayCards = document.querySelectorAll('.day-card');
    dayCards.forEach(card => {
        card.addEventListener('click', function() {
            const checkbox = this.querySelector('.day-checkbox');
            checkbox.checked = !checkbox.checked;
            
            if (checkbox.checked) {
                this.classList.add('selected');
            } else {
                this.classList.remove('selected');
            }
        });
    });
    
    // Select All/Deselect All button
    const selectAllBtn = document.getElementById('selectAllBtn');
    selectAllBtn.addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.day-checkbox');
        const allSelected = Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = !allSelected;
            const card = checkbox.closest('.day-card');
            if (!allSelected) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        });
        
        selectAllBtn.innerHTML = allSelected ? 
            '<i class="fas fa-check-square"></i> Select All' : 
            '<i class="fas fa-times"></i> Deselect All';
    });
    
    // Quick status toggle on double click
    dayCards.forEach(card => {
        card.addEventListener('dblclick', function() {
            const currentStatus = this.getAttribute('data-status');
            let newStatus;
            
            // Cycle through statuses: available -> reserved -> unavailable -> available
            if (currentStatus === 'available') {
                newStatus = 'reserved';
            } else if (currentStatus === 'reserved') {
                newStatus = 'unavailable';
            } else {
                newStatus = 'available';
            }
            
            // Update the UI immediately
            this.setAttribute('data-status', newStatus);
            this.className = 'day-card ' + newStatus;
            this.querySelector('.day-status').className = 'day-status status-' + newStatus;
            this.querySelector('.day-status').textContent = 
                newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
            
            // Get the day and laboratory values
            const day = this.getAttribute('data-day');
            const laboratory = document.querySelector('input[name="laboratory"]').value;
            
            // Send AJAX request to update the database
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `selected_days[]=${encodeURIComponent(day)}&laboratory=${encodeURIComponent(laboratory)}&action=${encodeURIComponent(newStatus)}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                // Optional: Show a success message
                console.log('Status updated successfully');
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert UI changes if update failed
                this.setAttribute('data-status', currentStatus);
                this.className = 'day-card ' + currentStatus;
                this.querySelector('.day-status').className = 'day-status status-' + currentStatus;
                this.querySelector('.day-status').textContent = 
                    currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1);
            });
        });
    });
});
</script>

