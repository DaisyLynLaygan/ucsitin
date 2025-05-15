<?php
include '../student/connection.php';

// Load required libraries
require '../vendor/autoload.php'; // Composer autoload

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use TCPDF; // For PDF generation

// Define foul words for censorship
$foulWords = ["shit", "fuck", "badword1", "badword2", "badword3"];

// Function to censor feedback
function censorFoulWords($text, $foulWords) {
    foreach ($foulWords as $word) {
        $pattern = "/\b" . preg_quote($word, '/') . "\b/i";
        $replacement = str_repeat('*', strlen($word));
        $text = preg_replace($pattern, $replacement, $text);
    }
    return $text;
}

// Get filter parameters from the URL
$lab = isset($_GET['lab']) ? $_GET['lab'] : '';
$purpose = isset($_GET['purpose']) ? $_GET['purpose'] : '';

// Build SQL WHERE conditions based on filters
$whereClauses = [];
if (!empty($lab)) {
    $whereClauses[] = "ss.lab = '$lab'";
}
if (!empty($purpose)) {
    $whereClauses[] = "ss.purpose = '$purpose'";
}

$whereClause = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : '';

// Check export format
$format = isset($_GET['format']) ? $_GET['format'] : 'csv';

// Query feedback data
$sql = "SELECT f.feedback, f.flagged, f.date_submitted, 
               s.firstname, s.lastname, s.course, 
               ss.lab, ss.purpose, ss.`sit-login`, ss.`sit-logout`
        FROM feedback f
        JOIN student_sitin ss ON f.sit_in_id = ss.sit_in_id
        JOIN student s ON f.idno = s.idno
        $whereClause
        ORDER BY f.date_submitted DESC";

$result = $conn->query($sql);

// Prepare data for export
$feedbackData = [];
while ($row = $result->fetch_assoc()) {
    $feedbackText = $row['flagged'] ? censorFoulWords($row['feedback'], $foulWords) : $row['feedback'];

    $feedbackData[] = [
        'Name' => $row['firstname'] . ' ' . $row['lastname'],
        'Course' => $row['course'],
        'Lab' => $row['lab'],
        'Purpose' => $row['purpose'],
        'Date' => date("M d, Y", strtotime($row['sit-login'])),
        'In Time' => date("h:i A", strtotime($row['sit-login'])),
        'Out Time' => date("h:i A", strtotime($row['sit-logout'])),
        'Feedback' => $feedbackText,
        'Submitted On' => date("F j, Y, g:i a", strtotime($row['date_submitted']))
    ];
}

// Prevent any output before headers
if (ob_get_length()) ob_end_clean();

if (empty($feedbackData)) {
    die("No feedback available for export.");
}

// Header for all formats (aligned center)
$headerText = "University of Cebu-Main\n";
$headerText .= "College of Computer Studies\n";
$headerText .= "Computer Laboratory Sitin Monitoring System Report\n\n";

// 📌 **CSV Export**
if ($format == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="feedback_report.csv"');
    $output = fopen('php://output', 'w');

    // Output centered header
    fputcsv($output, ["", "", $headerText, "", ""]);  // Empty cells before and after for alignment

    // Output headers for columns with spacing for better readability
    fputcsv($output, array_keys($feedbackData[0]));

    // Output data with an additional row spacing for readability
    foreach ($feedbackData as $row) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}

// 📌 **Excel Export**
elseif ($format == 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Add header with center alignment
    $sheet->setCellValue('A1', $headerText);

    // Style header (bold, larger font size, centered alignment)
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->mergeCells('A1:I1'); // Merge cells for header
    $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A1:I1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

    // Add Headers (column names)
    $headers = array_keys($feedbackData[0]);
    $sheet->fromArray([$headers], NULL, 'A3');

    // Style headers with bold and borders
    $sheet->getStyle('A3:I3')->getFont()->setBold(true);
    $sheet->getStyle('A3:I3')->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            ]
        ]
    ]);

    // Add Data
    $rowIndex = 4;
    foreach ($feedbackData as $row) {
        $sheet->fromArray(array_values($row), NULL, "A$rowIndex");

        // Add borders to each row
        $sheet->getStyle("A$rowIndex:I$rowIndex")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ]);

        $rowIndex++;
    }

    // Set headers for file download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="feedback_report.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// 📌 **PDF Export**
elseif ($format == 'pdf') {
    require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');

    $pdf = new TCPDF();
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 10);

    // Add Header (centered alignment)
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->MultiCell(0, 10, $headerText, 0, 'C');

    // Table Header
    $html = '<h2 style="text-align:center;">Feedback Report</h2>';
    $html .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">';
    $html .= '<tr style="font-weight: bold; background-color: #f2f2f2; text-align: center;">';

    foreach (array_keys($feedbackData[0]) as $header) {
        $html .= "<th>$header</th>";
    }

    $html .= '</tr>';

    $rowColor = "#ffffff";
    foreach ($feedbackData as $row) {
        $html .= '<tr style="background-color:' . $rowColor . ';">';
        foreach ($row as $value) {
            $html .= '<td>' . nl2br(htmlspecialchars($value)) . '</td>';
        }
        $html .= '</tr>';
        // Alternate row colors
        $rowColor = ($rowColor == "#ffffff") ? "#f9f9f9" : "#ffffff";
    }

    $html .= '</table>';

    $pdf->writeHTML($html, true, false, false, false, '');
    $pdf->Output('feedback_report.pdf', 'D');
    exit;
}

$conn->close();
?>
