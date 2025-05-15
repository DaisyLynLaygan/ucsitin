<?php
// This must be the VERY FIRST LINE - no whitespace before!
session_start();
$pageTitle = "Lab Schedule";

// Include database connection
include 'connection.php';

// Now include header after all potential header operations are complete
include 'header.php';

// Get current laboratory from query parameter (default to 524)
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
    color: #303f9f;
    margin: 0;
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
    color: #303f9f;
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
    border: 1px solid #303f9f;
    background: white;
    color: #303f9f;
}

.laboratory-btn:hover {
    background-color: #f8f9fa;
    border-color: #303f9f;
    color: #303f9f;
}

.laboratory-btn.active {
    background-color: #303f9f;
    border-color: #303f9f;
    color: white;
}

.schedule-container {
    background: white;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 20px;
}

.schedule-header {
    padding: 15px 20px;
    background-color: #f8f9fa;
    border-bottom: 1px solid #e0e0e0;
}

.schedule-title {
    font-size: 15px;
    font-weight: 500;
    color: #303f9f;
    margin: 0;
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
    transition: all 0.2s;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    min-height: 100px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.day-card.available {
    background-color: rgba(40, 167, 69, 0.1);
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.day-card.unavailable {
    background-color: rgba(220, 53, 69, 0.1);
    border: 1px solid rgba(220, 53, 69, 0.2);
}

.day-card.reserved {
    background-color: rgba(255, 193, 7, 0.1);
    border: 1px solid rgba(255, 193, 7, 0.2);
}

.day-name {
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 8px;
    color: #303f9f;
}

.day-status {
    font-size: 13px;
    padding: 4px 8px;
    border-radius: 4px;
    display: inline-block;
    font-weight: 500;
    color: #303f9f;
    background: #f8f9fa;
}

.status-available {
    background-color: #28a745;
    color: white;
}

.status-unavailable {
    background-color:  #dc3545;
    color: white;
}

.status-reserved {
    background-color: #ffc107;
    color: white;
}

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
    background-color:  #303f9f;
}

/* Legend styles */
.legend {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 13px;
}

.legend-color {
    width: 15px;
    height: 15px;
    border-radius: 3px;
}

.legend-available {
    background-color: #28a745;
}

.legend-reserved {
    background-color: #ffc107;
}

.legend-unavailable {
    background-color: #dc3545;
}

.legend-item span {
    color: #303f9f;
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
</style>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Lab Schedule</h1>
    </div>
    
    <!-- Legend -->
    <div class="legend">
        <div class="legend-item">
            <div class="legend-color legend-available"></div>
            <span>Available</span>
        </div>
        <div class="legend-item">
            <div class="legend-color legend-reserved"></div>
            <span>Reserved</span>
        </div>
        <div class="legend-item">
            <div class="legend-color legend-unavailable"></div>
            <span>Unavailable</span>
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
    
    <div class="schedule-container">
        <div class="schedule-header">
            <h2 class="schedule-title">Lab Schedule: Room <?= htmlspecialchars($current_laboratory) ?></h2>
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
            <div class="day-card <?= $status_class ?>">
                <div class="status-indicator"></div>
                <div class="day-name"><?= $day ?></div>
                <div class="day-status status-<?= $status_class ?>">
                    <?= $status_text ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

