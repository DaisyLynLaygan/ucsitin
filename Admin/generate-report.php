<?php
include '../student/connection.php';

// Load required libraries
require '../vendor/autoload.php'; // Composer autoload

// Correct inclusion of TCPDF without use statement
require_once('../vendor/tecnickcom/tcpdf/tcpdf.php'); // Include TCPDF correctly

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Capture the lab and purpose filter values
$labFilter = isset($_GET['lab']) ? $_GET['lab'] : '';
$purposeFilter = isset($_GET['purpose']) ? $_GET['purpose'] : '';

// Build SQL WHERE conditions based on filters
$whereClauses = ["ss.status = 'Completed'"]; // Status filter remains fixed
if (!empty($labFilter)) {
    $whereClauses[] = "ss.lab = '$labFilter'";
}
if (!empty($purposeFilter)) {
    $whereClauses[] = "ss.purpose = '$purposeFilter'";
}

$whereClause = "WHERE " . implode(" AND ", $whereClauses);

// Check export format
$format = isset($_GET['format']) ? $_GET['format'] : 'csv';

// Query sit-in data with lab and purpose filters (correct column names)
$sql = "SELECT ss.sit_in_id, ss.idno, s.firstname, s.lastname, 
               ss.purpose, ss.lab, ss.`sit-login`, ss.`sit-logout`
        FROM student_sitin ss
        JOIN student s ON ss.idno = s.idno
        $whereClause
        ORDER BY ss.`sit-login` DESC";  // Corrected column names

$result = $conn->query($sql);

// Prepare data for export
$sitinData = [];
while ($row = $result->fetch_assoc()) {
    $sitinData[] = [
        'ID Number' => $row['idno'],
        'Name' => $row['firstname'] . ' ' . $row['lastname'],
        'Purpose' => $row['purpose'],
        'Lab' => $row['lab'],
        'Sit-In Date' => date("M d, Y", strtotime($row['sit-login'])),
        'Sit-In Time' => date("h:i A", strtotime($row['sit-login'])),
        'Sit-Out Time' => date("h:i A", strtotime($row['sit-logout'])),
    ];
}

// Prevent any output before headers
if (ob_get_length()) ob_end_clean();

if (empty($sitinData)) {
    die("No sit-in records available for export.");
}

// Header for all formats (aligned center)
$headerText = "University of Cebu-Main\n";
$headerText .= "College of Computer Studies\n";
$headerText .= "Computer Laboratory Sitin Monitoring System Report\n\n";

// 📌 **CSV Export**
if ($format == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sitin_report.csv"');
    $output = fopen('php://output', 'w');

    // Output centered header
    fputcsv($output, ["", "", $headerText, "", ""]);  // Empty cells before and after for alignment

    // Output headers for columns with spacing for better readability
    fputcsv($output, array_keys($sitinData[0]));

    // Output data with an additional row spacing for readability
    foreach ($sitinData as $row) {
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
    $sheet->mergeCells('A1:G1'); // Merge cells for header
    $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A1:G1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

    // Add Headers (column names)
    $headers = array_keys($sitinData[0]);
    $sheet->fromArray([$headers], NULL, 'A3');

    // Style headers with bold and borders
    $sheet->getStyle('A3:G3')->getFont()->setBold(true);
    $sheet->getStyle('A3:G3')->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            ]
        ]
    ]);

    // Add Data
    $rowIndex = 4;
    foreach ($sitinData as $row) {
        $sheet->fromArray(array_values($row), NULL, "A$rowIndex");

        // Add borders to each row
        $sheet->getStyle("A$rowIndex:G$rowIndex")->applyFromArray([
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
    header('Content-Disposition: attachment; filename="sitin_report.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// 📌 **PDF Export**
elseif ($format == 'pdf') {
    $pdf = new TCPDF();
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 10);

    // Add Header (centered alignment)
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->MultiCell(0, 10, $headerText, 0, 'C');

    // Table Header
    $html = '<h2 style="text-align:center;">Sit-In Report</h2>';
    $html .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">';
    $html .= '<tr style="font-weight: bold; background-color: #f2f2f2; text-align: center;">';

    foreach (array_keys($sitinData[0]) as $header) {
        $html .= "<th>$header</th>";
    }

    $html .= '</tr>';

    $rowColor = "#ffffff";
    foreach ($sitinData as $row) {
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
    $pdf->Output('sitin_report.pdf', 'D');
    exit;
}

$conn->close();
?>
