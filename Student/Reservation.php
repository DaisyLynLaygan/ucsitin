<?php
session_start();
$pageTitle = "Reservation System";
include 'header.php';
include '../student/connection.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['make_reservation'])) {
        $student_id = $_SESSION['idno'];
        $laboratory = $_POST['laboratory'];
        $pc_number = $_POST['pc_number'];
        $purpose = $_POST['purpose'];
        $reservation_date = $_POST['reservation_date'];
        $time_slot = $_POST['time_slot'];
        
        // Check if the PC is available
        $check_stmt = $conn->prepare("SELECT status FROM pc_availability WHERE laboratory = ? AND pc_number = ?");
        $check_stmt->bind_param("si", $laboratory, $pc_number);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $pc_status = $check_result->fetch_assoc()['status'];
            if ($pc_status !== 'available') {
                echo "<script>alert('The selected PC is not available. Please choose another one.');</script>";
            } else {
                // Insert reservation
                $stmt = $conn->prepare("INSERT INTO reservations (student_id, laboratory, pc_number, purpose, reservation_date, time_slot, status) 
                                      VALUES (?, ?, ?, ?, ?, ?, 'pending')");
                $stmt->bind_param("ssisss", $student_id, $laboratory, $pc_number, $purpose, $reservation_date, $time_slot);
                
                if ($stmt->execute()) {
                    // Update PC availability
                    $update_stmt = $conn->prepare("UPDATE pc_availability SET status = 'reserved' WHERE laboratory = ? AND pc_number = ?");
                    $update_stmt->bind_param("si", $laboratory, $pc_number);
                    $update_stmt->execute();
                    $update_stmt->close();
                    
                    echo "<script>alert('Reservation request submitted successfully!'); window.location.href='reservation.php';</script>";
                } else {
                    echo "<script>alert('Error submitting reservation: " . $conn->error . "');</script>";
                }
                $stmt->close();
            }
        }
        $check_stmt->close();
    } elseif (isset($_POST['update_reservation'])) {
        $reservation_id = $_POST['reservation_id'];
        $purpose = $_POST['purpose'];
        $reservation_date = $_POST['reservation_date'];
        $time_slot = $_POST['time_slot'];
        
        $stmt = $conn->prepare("UPDATE reservations SET purpose = ?, reservation_date = ?, time_slot = ? WHERE id = ? AND student_id = ?");
        $stmt->bind_param("sssis", $purpose, $reservation_date, $time_slot, $reservation_id, $_SESSION['idno']);
        
        if ($stmt->execute()) {
            echo "<script>alert('Reservation updated successfully!'); window.location.href='reservation.php';</script>";
        } else {
            echo "<script>alert('Error updating reservation: " . $conn->error . "');</script>";
        }
        $stmt->close();
    }
}

// Handle reservation deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // First get reservation details to update pc_availability
    $stmt = $conn->prepare("SELECT laboratory, pc_number FROM reservations WHERE id = ? AND student_id = ?");
    $stmt->bind_param("is", $delete_id, $_SESSION['idno']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $reservation = $result->fetch_assoc();
        $laboratory = $reservation['laboratory'];
        $pc_number = $reservation['pc_number'];
        
        // Delete the reservation
        $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ? AND student_id = ?");
        $stmt->bind_param("is", $delete_id, $_SESSION['idno']);
        
        if ($stmt->execute()) {
            // Mark PC as available again
            $update_stmt = $conn->prepare("UPDATE pc_availability SET status = 'available' WHERE laboratory = ? AND pc_number = ?");
            $update_stmt->bind_param("si", $laboratory, $pc_number);
            $update_stmt->execute();
            $update_stmt->close();
            
            echo "<script>alert('Reservation deleted successfully!'); window.location.href='reservation.php';</script>";
        } else {
            echo "<script>alert('Error deleting reservation: " . $conn->error . "');</script>";
        }
        $stmt->close();
    }
}

// Get student's reservations
$reservations = [];
$stmt = $conn->prepare("SELECT * FROM reservations WHERE student_id = ? ORDER BY reservation_date DESC, time_slot ASC");
$stmt->bind_param("s", $_SESSION['idno']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $reservations = $result->fetch_all(MYSQLI_ASSOC);
}
$stmt->close();

// Get available labs
$available_labs = [];
$stmt = $conn->prepare("SELECT DISTINCT laboratory FROM lab_schedules WHERE status = 'available'");
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $available_labs = $result->fetch_all(MYSQLI_ASSOC);
}
$stmt->close();
?>

<style>
    .container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 0 15px;
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
    
    .btn,
    .btn-primary,
    .btn-success,
    .btn-danger,
    .btn-warning,
    .btn-outline-secondary {
        color: #3f51b5;
    }
    
    .btn {
        padding: 8px 15px;
        border-radius: 4px;
        font-size: 14px;
        transition: all 0.2s;
    }
    
    .btn-primary {
        background-color: #3498db;
        border-color: #3498db;
        color: white;
    }
    
    .btn-primary:hover {
        background-color: #2980b9;
        border-color: #2980b9;
    }
    
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
    }
    
    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }
    
    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
    }
    
    .btn-outline-secondary {
        border-color: #bdc3c7;
        color: #7f8c8d;
    }
    
    .table-container {
        background: white;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 20px;
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    
    .table th {
        background-color: #3f51b5;
        color: white;
        padding: 12px 15px;
        text-align: center;
    }
    
    .table td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        text-align: center;
        vertical-align: middle;
        color: #3f51b5;
    }
    
    .table tr:last-child td {
        border-bottom: none;
    }
    
    .table tr:hover {
        background-color: #f8f9fa;
    }
    
    .badge,
    .badge-pending,
    .badge-approved,
    .badge-rejected,
    .badge-completed {
        color: #3f51b5;
    }
    
    .badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .badge-pending {
        background-color: #3f51b5;
        color: white;
    }
    
    .badge-approved {
        background-color: #28a745;
        color: white;
    }
    
    .badge-rejected {
        background-color: #dc3545;
        color: white;
    }
    
    .badge-completed {
        background-color: #17a2b8;
        color: white;
    }
    
    .pc-card {
        padding: 10px;
        border-radius: 6px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        margin-bottom: 10px;
        border: 1px solid #ddd;
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
        cursor: not-allowed;
        opacity: 0.6;
    }
    
    .pc-card.selected {
        border: 2px solid  #3f51b5;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
    }
    
    .pc-number {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 5px;
    }
    
    .pc-status {
        font-size: 12px;
        padding: 2px 6px;
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
    
    .pc-checkbox {
        display: none;
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            overflow-x: auto;
        }
        
        .action-btns {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
    }
    
    .text-muted,
    .text-center .text-muted,
    .text-muted small {
        color: #3f51b5 !important;
    }
    
    .form-label {
        color: #3f51b5;
    }
</style>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Computer Lab Reservation</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reservationModal">
            <i class="fas fa-plus"></i> Make Reservation
        </button>
    </div>
    
    <div class="table-container">
        <div class="table-responsive">
        <table class="table table-hover">
    <thead>
        <tr>
            <!-- Remove this line: <th>Reservation ID</th> -->
            <th>Laboratory</th>
            <th>PC Number</th>
            <th>Date</th>
            <th>Time In</th>
            <th>Purpose</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($reservations)): ?>
            <?php foreach ($reservations as $reservation): ?>
                <tr>
                    <!-- Remove this line: <td><?= htmlspecialchars($reservation['id']) ?></td> -->
                    <td><?= htmlspecialchars($reservation['laboratory']) ?></td>
                    <td><?= htmlspecialchars($reservation['pc_number']) ?></td>
                    <td><?= date('M d, Y', strtotime($reservation['reservation_date'])) ?></td>
                    <td><?= htmlspecialchars($reservation['time_slot']) ?></td>
                    <td><?= htmlspecialchars($reservation['purpose']) ?></td>
                    <td>
                        <?php 
                        $status_class = '';
                        switch(strtolower($reservation['status'])) {
                            case 'pending':
                                $status_class = 'badge-pending';
                                break;
                            case 'approved':
                                $status_class = 'badge-approved';
                                break;
                            case 'rejected':
                                $status_class = 'badge-rejected';
                                break;
                            case 'completed':
                                $status_class = 'badge-completed';
                                break;
                            default:
                                $status_class = 'badge-pending';
                        }
                        ?>
                        <span class="badge <?= $status_class ?>"><?= ucfirst($reservation['status']) ?></span>
                    </td>
                    <td class="position-relative">
                        <div class="d-flex gap-2 action-btns">
                            <?php if ($reservation['status'] == 'pending'): ?>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editReservationModal" 
                                    data-id="<?= $reservation['id'] ?>"
                                    data-laboratory="<?= htmlspecialchars($reservation['laboratory']) ?>"
                                    data-pc_number="<?= htmlspecialchars($reservation['pc_number']) ?>"
                                    data-purpose="<?= htmlspecialchars($reservation['purpose']) ?>"
                                    data-reservation_date="<?= htmlspecialchars($reservation['reservation_date']) ?>"
                                    data-time_slot="<?= htmlspecialchars($reservation['time_slot']) ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <a href="reservation.php?delete_id=<?= $reservation['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this reservation?');">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">No actions available</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <!-- Update colspan from 8 to 7 since we removed one column -->
                <td colspan="7" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                        <h5 class="text-muted">No reservations found</h5>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
        </div>
    </div>
</div>

<!-- Make Reservation Modal -->
<div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reservationModalLabel"><i class="fas fa-calendar-plus me-2"></i>Make New Reservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="reservationForm">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="laboratory" class="form-label">Laboratory</label>
                            <select class="form-select" name="laboratory" id="laboratory" required>
                                <option value="" disabled selected>Select Laboratory</option>
                                <?php foreach ($available_labs as $lab): ?>
                                    <option value="<?= htmlspecialchars($lab['laboratory']) ?>"><?= htmlspecialchars($lab['laboratory']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="reservation_date" class="form-label">Date</label>
                            <input type="date" class="form-control" name="reservation_date" id="reservation_date" min="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="time_slot" class="form-label">Time In</label>
                            <select class="form-select" name="time_slot" id="time_slot" required>
                                <option value="" disabled selected>Select Time Slot</option>
                                <option value="7:30-8:30 AM">7:30-8:30 AM</option>
                                <option value="8:30-9:30 AM">8:30-9:30 AM</option>
                                <option value="9:30-10:30 AM">9:30-10:30 AM</option>
                                <option value="10:30-11:30 AM">10:30-11:30 AM</option>
                                <option value="11:30 AM-12:30 PM">11:30 AM-12:30 PM</option>
                                <option value="12:30-1:30 PM">12:30-1:30 PM</option>
                                <option value="1:30-2:30 PM">1:30-2:30 PM</option>
                                <option value="2:30-3:30 PM">2:30-3:30 PM</option>
                                <option value="3:30-4:30 PM">3:30-4:30 PM</option>
                                <option value="4:30-5:30 PM">4:30-5:30 PM</option>
                                <option value="5:30-6:30 PM">5:30-6:30 PM</option>
                                <option value="6:30-7:30 PM">6:30-7:30 PM</option>
                                <option value="7:30-8:30 PM">7:30-8:30 PM</option>
                                <option value="8:30-9:30 PM">8:30-9:30 PM</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="purpose" class="form-label">Purpose</label>
                            <select class="form-select" name="purpose" id="purpose" required>
                            <option value="" disabled selected>Select Purpose</option>
                            <option value="C Programming">C Programming</option>
                            <option value="Java Programming">Java Programming</option>
                            <option value="C# Programming">C# Programming</option>
                            <option value="System Integration & Architecture">System Integration & Architecture</option>
                            <option value="Embeded Systems & IoT">Embeded Systems & IoT</option>
                            <option value="Digital Logic & Design">Digital Logic & Design</option>
                            <option value="Computer Application">Computer Application</option>
                            <option value="Database">Database</option>
                            <option value="Project Management">Project Management</option>
                            <option value="Python Programming">Python Programming</option>
                            <option value="Mobile Application">Mobile Application</option>
                            <option value="Web Design">Web Design</option>
                            <option value="Php Programming">Php Programming</option>
                            <option value="Others">Others</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Available PCs</label>
                        <div class="row" id="pcGrid">
                            <!-- PCs will be loaded here via AJAX -->
                            <div class="col-12 text-center">
                                <p class="text-muted">Please select a laboratory, date, and time slot first</p>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="pc_number" id="selected_pc">
                    <input type="hidden" name="make_reservation" value="1">
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                            <i class="fas fa-paper-plane me-2"></i> Submit Reservation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Reservation Modal -->
<div class="modal fade" id="editReservationModal" tabindex="-1" aria-labelledby="editReservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editReservationModalLabel"><i class="fas fa-edit me-2"></i>Edit Reservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="editReservationForm">
                    <input type="hidden" name="reservation_id" id="edit_reservation_id">
                    <input type="hidden" name="update_reservation" value="1">
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Laboratory</label>
                            <input type="text" class="form-control" id="edit_laboratory" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">PC Number</label>
                            <input type="text" class="form-control" id="edit_pc_number" readonly>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="edit_reservation_date" class="form-label">Date</label>
                            <input type="date" class="form-control" name="reservation_date" id="edit_reservation_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_time_slot" class="form-label">Time In</label>
                            <select class="form-select" name="time_slot" id="edit_time_slot" required>
                                <option value="7:30-8:30 AM">7:30-8:30 AM</option>
                                <option value="8:30-9:30 AM">8:30-9:30 AM</option>
                                <option value="9:30-10:30 AM">9:30-10:30 AM</option>
                                <option value="10:30-11:30 AM">10:30-11:30 AM</option>
                                <option value="11:30 AM-12:30 PM">11:30 AM-12:30 PM</option>
                                <option value="12:30-1:30 PM">12:30-1:30 PM</option>
                                <option value="1:30-2:30 PM">1:30-2:30 PM</option>
                                <option value="2:30-3:30 PM">2:30-3:30 PM</option>
                                <option value="3:30-4:30 PM">3:30-4:30 PM</option>
                                <option value="4:30-5:30 PM">4:30-5:30 PM</option>
                                <option value="5:30-6:30 PM">5:30-6:30 PM</option>
                                <option value="6:30-7:30 PM">6:30-7:30 PM</option>
                                <option value="7:30-8:30 PM">7:30-8:30 PM</option>
                                <option value="8:30-9:30 PM">8:30-9:30 PM</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="edit_purpose" class="form-label">Purpose</label>
                        <select class="form-select" name="purpose" id="edit_purpose" required>
                            <option value="" disabled selected>Select Purpose</option>
                            <option value="C Programming">C Programming</option>
                            <option value="Java Programming">Java Programming</option>
                            <option value="C# Programming">C# Programming</option>
                            <option value="System Integration & Architecture">System Integration & Architecture</option>
                            <option value="Embeded Systems & IoT">Embeded Systems & IoT</option>
                            <option value="Digital Logic & Design">Digital Logic & Design</option>
                            <option value="Computer Application">Computer Application</option>
                            <option value="Database">Database</option>
                            <option value="Project Management">Project Management</option>
                            <option value="Python Programming">Python Programming</option>
                            <option value="Mobile Application">Mobile Application</option>
                            <option value="Web Design">Web Design</option>
                            <option value="Php Programming">Php Programming</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Update Reservation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Edit modal handler
const editModal = document.getElementById('editReservationModal');
if (editModal) {
    editModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const laboratory = button.getAttribute('data-laboratory');
        const pc_number = button.getAttribute('data-pc_number');
        const purpose = button.getAttribute('data-purpose');
        const reservation_date = button.getAttribute('data-reservation_date');
        const time_slot = button.getAttribute('data-time_slot');
        
        editModal.querySelector('#edit_reservation_id').value = id;
        editModal.querySelector('#edit_laboratory').value = laboratory;
        editModal.querySelector('#edit_pc_number').value = pc_number;
        editModal.querySelector('#edit_purpose').value = purpose;
        editModal.querySelector('#edit_reservation_date').value = reservation_date;
        editModal.querySelector('#edit_time_slot').value = time_slot;
    });
}

function loadAvailablePCs() {
    const lab = document.getElementById('laboratory').value;
    const date = document.getElementById('reservation_date').value;
    const time = document.getElementById('time_slot').value;
    const pcGrid = document.getElementById('pcGrid');
    
    // Clear previous selection
    document.getElementById('selected_pc').value = '';
    document.getElementById('submitBtn').disabled = true;
    
    // Validate inputs
    if (!lab || !date || !time) {
        pcGrid.innerHTML = '<div class="col-12 text-center"><p class="text-muted">Please select laboratory, date and time slot first</p></div>';
        return;
    }
    
    // Show loading state
    pcGrid.innerHTML = '<div class="col-12 text-center"><p><i class="fas fa-spinner fa-spin"></i> Loading available PCs...</p></div>';
    
    // Create form data
    const formData = new FormData();
    formData.append('laboratory', lab);
    formData.append('date', date);
    formData.append('time', time);
    
    // Use correct path to the PHP file
    const url = './get-avialable-pcs.php?t=' + new Date().getTime();
    
    // Make AJAX request
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            // Get more detailed error info
            return response.text().then(text => {
                throw new Error(`Server error: ${response.status} - ${text}`);
            });
        }
        return response.text();
    })
    .then(data => {
        pcGrid.innerHTML = data;
        
        // Add click handlers to available PCs
        document.querySelectorAll('.pc-card.available').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.pc-card').forEach(c => {
                    c.classList.remove('selected');
                });
                
                this.classList.add('selected');
                document.getElementById('selected_pc').value = this.dataset.pcNumber;
                document.getElementById('submitBtn').disabled = false;
            });
        });
    })
    .catch(error => {
        console.error('Fetch error:', error);
        pcGrid.innerHTML = `
            <div class="col-12 text-center text-danger">
                <p>Failed to load available PCs</p>
                <p><small>${error.message}</small></p>
                <p class="small">Please check console for details</p>
                <button class="btn btn-sm btn-primary mt-2" onclick="loadAvailablePCs()">
                    <i class="fas fa-redo"></i> Try Again
                </button>
            </div>
        `;
    });
}

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Set up change event listeners
    const labSelect = document.getElementById('laboratory');
    const dateInput = document.getElementById('reservation_date');
    const timeSelect = document.getElementById('time_slot');
    
    if (labSelect && dateInput && timeSelect) {
        labSelect.addEventListener('change', loadAvailablePCs);
        dateInput.addEventListener('change', loadAvailablePCs);
        timeSelect.addEventListener('change', loadAvailablePCs);
        
        // Set default date to today
        dateInput.valueAsDate = new Date();
        
        // Debug: Log current values
        console.log('Initial values:', {
            lab: labSelect.value,
            date: dateInput.value,
            time: timeSelect.value
        });
        
        // Initial load if all required fields are filled
        if (labSelect.value && dateInput.value && timeSelect.value) {
            loadAvailablePCs();
        }
    } else {
        console.error('One or more form elements not found');
    }
});
</script>
