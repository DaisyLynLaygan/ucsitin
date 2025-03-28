<?php
session_start();
include './connection.php';

$conn = new mysqli('localhost', 'root', '', 'sitin');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch feedback data
$sql = "SELECT idno, laboratory, feedback, date FROM sit_in WHERE feedback IS NOT NULL ORDER BY date DESC";
$result = $conn->query($sql);
$feedbackData = $result->fetch_all(MYSQLI_ASSOC);

$format = $_GET['format'] ?? 'csv';

if ($format == "csv") {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="feedback.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID Number', 'Laboratory', 'Feedback', 'Date']);
    foreach ($feedbackData as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
} elseif ($format == "pdf") {
    require('fpdf/fpdf.php');
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, "ID Number");
    $pdf->Cell(30, 10, "Lab");
    $pdf->Cell(100, 10, "Feedback");
    $pdf->Cell(40, 10, "Date");
    $pdf->Ln();
    
    foreach ($feedbackData as $row) {
        $pdf->Cell(40, 10, $row['idno']);
        $pdf->Cell(30, 10, $row['laboratory']);
        $pdf->Cell(100, 10, $row['feedback']);
        $pdf->Cell(40, 10, $row['date']);
        $pdf->Ln();
    }
    
    $pdf->Output('D', 'feedback.pdf');
} elseif ($format == "doc") {
    header("Content-type: application/vnd.ms-word");
    header("Content-Disposition: attachment;Filename=feedback.doc");
    echo "<html><body><table border='1'><tr><th>ID Number</th><th>Laboratory</th><th>Feedback</th><th>Date</th></tr>";
    
    foreach ($feedbackData as $row) {
        echo "<tr><td>{$row['idno']}</td><td>{$row['laboratory']}</td><td>{$row['feedback']}</td><td>{$row['date']}</td></tr>";
    }
    
    echo "</table></body></html>";
}
exit;
?>
