<?php
ob_start(); // Start output buffering to prevent accidental output

include '../student/connection.php'; // Ensure the path is correct
require '../vendor/autoload.php'; // Composer autoload
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
require_once('../vendor/tecnickcom/tcpdf/tcpdf.php'); // TCPDF

$type = isset($_GET['type']) ? $_GET['type'] : 'csv';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Fetch data
$where = "WHERE ss.status = 'Completed'";
if ($startDate && $endDate) {
    $where .= " AND DATE(ss.`sit-login`) BETWEEN '$startDate' AND '$endDate'";
}

$sql = "SELECT ss.sit_in_id, ss.idno, s.firstname, s.middlename, s.lastname, ss.purpose, ss.lab, ss.`sit-login`, ss.`sit-logout`
        FROM student_sitin ss
        INNER JOIN student s ON ss.idno = s.idno
        $where
        ORDER BY ss.sit_in_id ASC";

$result = $conn->query($sql);

// Prepare records
$records = [];
while ($row = $result->fetch_assoc()) {
    $records[] = $row;
}

if ($type === 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Headers
    $sheet->fromArray(['#', 'ID Number', 'Student Name', 'Purpose', 'Lab', 'Sit-In Time', 'Sit-Out Time'], NULL, 'A1');

    // Data
    $rowNum = 2;
    foreach ($records as $index => $row) {
        $sheet->fromArray([
            $index + 1,
            $row['idno'],
            $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname'],
            $row['purpose'],
            $row['lab'],
            $row['sit-login'],
            $row['sit-logout']
        ], NULL, 'A' . $rowNum++);
    }

    // Clear any previous output to prevent file corruption
    if (ob_get_length()) ob_end_clean();

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="sit_in_records.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;

} elseif ($type === 'pdf') {
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    $html = '<h2>Sit-In Records</h2><table border="1" cellpadding="4"><thead><tr>
                <th>#</th><th>ID Number</th><th>Student Name</th><th>Purpose</th><th>Lab</th><th>Sit-In Time</th><th>Sit-Out Time</th></tr></thead><tbody>';
    foreach ($records as $index => $row) {
        $html .= '<tr>
            <td>' . ($index + 1) . '</td>
            <td>' . $row['idno'] . '</td>
            <td>' . $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname'] . '</td>
            <td>' . $row['purpose'] . '</td>
            <td>' . $row['lab'] . '</td>
            <td>' . $row['sit-login'] . '</td>
            <td>' . $row['sit-logout'] . '</td>
        </tr>';
    }
    $html .= '</tbody></table>';

    if (ob_get_length()) ob_end_clean();

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('sit_in_records.pdf', 'D');
    exit;

} else { // CSV default
    if (ob_get_length()) ob_end_clean();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="sit_in_records.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['#', 'ID Number', 'Student Name', 'Purpose', 'Lab', 'Sit-In Time', 'Sit-Out Time']);

    foreach ($records as $index => $row) {
        fputcsv($output, [
            $index + 1,
            $row['idno'],
            $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname'],
            $row['purpose'],
            $row['lab'],
            $row['sit-login'],
            $row['sit-logout']
        ]);
    }
    fclose($output);
    exit;
}
?>
