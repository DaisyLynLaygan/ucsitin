<?php
// This must be the VERY FIRST LINE - no whitespace before!
session_start();
$pageTitle = "Computer Lab Management";

// Include database connection BEFORE any potential header redirects
include '../student/connection.php';

// Handle PC status updates before any output
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['selected_pcs']) && isset($_POST['laboratory']) && isset($_POST['action'])) {
        $selected_pcs = $_POST['selected_pcs'];
        $laboratory = $_POST['laboratory'];
        $action = $_POST['action'];
        
        // Validate action
        $valid_actions = ['available', 'unavailable'];
        if (!in_array($action, $valid_actions)) {
            die("Invalid action");
        }
        
        // Update each selected PC
        foreach ($selected_pcs as $pc_id) {
            // Validate PC ID
            if (!is_numeric($pc_id)) {
                continue;
            }
            
            $stmt = $conn->prepare("UPDATE pc_availability SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND laboratory = ?");
            $stmt->bind_param("sis", $action, $pc_id, $laboratory);
            $stmt->execute();
            $stmt->close();
        }
        
        $_SESSION['success_message'] = 'PC status updated successfully!';
        header("Location: computer_lab_management.php?laboratory=".urlencode($laboratory));
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

// Get current laboratory from query parameter
$current_laboratory = isset($_GET['laboratory']) ? $_GET['laboratory'] : '524';

// Fetch all PC data for the current laboratory
$pc_data = [];
$stmt = $conn->prepare("SELECT id, pc_number, status FROM pc_availability WHERE laboratory = ? ORDER BY pc_number");
$stmt->bind_param("s", $current_laboratory);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $pc_data[] = $row;
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

.pc-management-container {
    background: white;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 20px;
}

.pc-management-header {
    padding: 15px 20px;
    background-color: #f8f9fa;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.pc-management-title {
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

.pcs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 15px;
    padding: 20px;
}

.pc-card {
    padding: 15px;
    border-radius: 6px;
    text-align: center;
    transition: all 0.2s;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    position: relative;
    overflow: hidden;
    border: 1px solid transparent;
    min-height: 60px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    cursor: pointer;
}

.pc-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 3px 6px rgba(0,0,0,0.1);
}

.pc-card.available {
    background-color: rgba(40, 167, 69, 0.1);
    border-color: rgba(40, 167, 69, 0.2);
}

.pc-card.unavailable {
    background-color: rgba(220, 53, 69, 0.1);
    border-color: rgba(220, 53, 69, 0.2);
}

.pc-card.selected {
    border: 2px solid #3f51b5;
    box-shadow: 0 0 0 3px rgba(63,81,181, 0.15);
}

.pc-number {
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 5px;
    color: #3f51b5;
}

.pc-status {
    font-size: 12px;
    padding: 3px 6px;
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

.status-indicator {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.available .status-indicator {
    background-color: #28a745;
}

.unavailable .status-indicator {
    background-color: #dc3545;
}

.pc-checkbox {
    position: absolute;
    top: 5px;
    left: 5px;
    width: 16px;
    height: 16px;
    opacity: 0;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .pcs-grid {
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 10px;
        padding: 15px;
    }
    
    .pc-card {
        padding: 10px;
        min-height: 50px;
    }
    
    .pc-number {
        font-size: 14px;
    }
    
    .pc-status {
        font-size: 11px;
    }
}

@media (max-width: 576px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .pcs-grid {
        grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
    }
    
    .action-buttons {
        flex-wrap: wrap;
        justify-content: flex-end;
    }
}

.count-badge {
    font-size: 13px;
    padding: 4px 8px;
    border-radius: 4px;
    margin-left: 8px;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-danger {
    background-color: #dc3545;
    color: white;
}
</style>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Computer Lab Management</h1>
        <div>
            <a href="labschedule.php" class="nav-btn btn-primary">
                <i class="fas fa-calendar-alt"></i> Lab Schedule
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
    
    <form method="POST" id="pcManagementForm">
        <input type="hidden" name="laboratory" value="<?= htmlspecialchars($current_laboratory) ?>">
        
        <div class="pc-management-container">
            <div class="pc-management-header">
                <h2 class="pc-management-title">
                    PC Availability: Room <?= htmlspecialchars($current_laboratory) ?>
                    <span class="badge badge-success">Available: <?= count(array_filter($pc_data, function($pc) { return $pc['status'] === 'available'; })) ?></span>
                    <span class="badge badge-danger">Unavailable: <?= count(array_filter($pc_data, function($pc) { return $pc['status'] === 'unavailable'; })) ?></span>
                </h2>
                <div class="action-buttons">
                    <button type="button" class="action-btn btn-outline-secondary" id="selectAllBtn">
                        <i class="fas fa-check-square"></i> Select All
                    </button>
                    <button type="submit" class="action-btn btn-success" name="action" value="available">
                        <i class="fas fa-check-circle"></i> Available
                    </button>
                    <button type="submit" class="action-btn btn-danger" name="action" value="unavailable">
                        <i class="fas fa-times-circle"></i> Unavailable
                    </button>
                </div>
            </div>
            
            <!-- PCs Grid -->
            <div class="pcs-grid">
                <?php foreach ($pc_data as $pc): 
                    $status_class = $pc['status'];
                    $status_text = ucfirst($pc['status']);
                ?>
                <div class="pc-card <?= $status_class ?>" 
                     data-pc-id="<?= $pc['id'] ?>" 
                     data-status="<?= $status_class ?>">
                    <input type="checkbox" name="selected_pcs[]" value="<?= $pc['id'] ?>" class="pc-checkbox">
                    <div class="status-indicator"></div>
                    <div class="pc-number">PC <?= $pc['pc_number'] ?></div>
                    <div class="pc-status status-<?= $status_class ?>">
                        <?= $status_text ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle PC selection on click
    const pcCards = document.querySelectorAll('.pc-card');
    pcCards.forEach(card => {
        card.addEventListener('click', function() {
            const checkbox = this.querySelector('.pc-checkbox');
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
        const checkboxes = document.querySelectorAll('.pc-checkbox');
        const allSelected = Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = !allSelected;
            const card = checkbox.closest('.pc-card');
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
    pcCards.forEach(card => {
        card.addEventListener('dblclick', function() {
            const currentStatus = this.getAttribute('data-status');
            const newStatus = currentStatus === 'available' ? 'unavailable' : 'available';
            const pcId = this.getAttribute('data-pc-id');
            const laboratory = document.querySelector('input[name="laboratory"]').value;
            
            // Update the UI immediately
            this.setAttribute('data-status', newStatus);
            this.className = 'pc-card ' + newStatus;
            this.querySelector('.pc-status').className = 'pc-status status-' + newStatus;
            this.querySelector('.pc-status').textContent = 
                newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
            
            // Create a form data object
            const formData = new FormData();
            formData.append('selected_pcs[]', pcId);
            formData.append('laboratory', laboratory);
            formData.append('action', newStatus);
            
            // Send AJAX request to update the database
            fetch('computer_lab_management.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                // Update the counts in the header
                const availableCount = document.querySelectorAll('.pc-card.available').length;
                const unavailableCount = document.querySelectorAll('.pc-card.unavailable').length;
                
                document.querySelector('.badge-success').textContent = 'Available: ' + availableCount;
                document.querySelector('.badge-danger').textContent = 'Unavailable: ' + unavailableCount;
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert UI changes if update failed
                this.setAttribute('data-status', currentStatus);
                this.className = 'pc-card ' + currentStatus;
                this.querySelector('.pc-status').className = 'pc-status status-' + currentStatus;
                this.querySelector('.pc-status').textContent = 
                    currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1);
            });
        });
    });
});
</script>

