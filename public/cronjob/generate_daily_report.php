<?php
ini_set('display_errors',1);  error_reporting(E_ALL);
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$db_server        = '127.0.0.1';
$db_user          = 'root';
$db_password      = 'Mag1clean@888';
$db_name          = 'db_magiclean';
$conn 			  = new mysqli($db_server,$db_user,$db_password,$db_name) or die (mysqli_error($conn));

$s1 = "SELECT * FROM tbl_user";
$h1 = mysqli_query($conn, $s1) or die (mysqli_error($conn));

$s2 = "SELECT *,date_format(a.created_at, '%d-%m-%Y') as created_at FROM tbl_leaderboard a INNER JOIN tbl_user b USING (email)";
$h2 = mysqli_query($conn, $s2) or die (mysqli_error($conn));

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Accumulative Report');
$sheet->setCellValue('A1', 'Accumulative Report')->getStyle('A1')->getFont()->setBold( true )->setSize(16);
$sheet->setCellValue('A3', 'No.')->getStyle('A3')->getFont()->setBold( true );
$sheet->setCellValue('B3', 'Date')->getStyle('B3')->getFont()->setBold( true );
$sheet->setCellValue('C3', 'Time')->getStyle('C3')->getFont()->setBold( true );
$sheet->setCellValue('D3', 'Name')->getStyle('D3')->getFont()->setBold( true );
$sheet->setCellValue('E3', 'Email')->getStyle('E3')->getFont()->setBold( true );
$sheet->setCellValue('F3', 'Checked to consent receiving marketing materials via email')->getStyle('F3')->getFont()->setBold( true );
$sheet->setCellValue('G3', 'Best Score')->getStyle('G3')->getFont()->setBold( true );
$sheet->setCellValue('H3', 'Score')->getStyle('H3')->getFont()->setBold( true );
$sheet->setCellValue('I3', 'EDM Send')->getStyle('I3')->getFont()->setBold( true );
$sheet->setCellValue('J3', 'Total time in game')->getStyle('J3')->getFont()->setBold( true );
$i=4;
$no=1;
while($r1 = mysqli_fetch_assoc($h1)) {
    $sheet->setCellValue("A$i",$no);
$i++;
$no++;
}

$styleArray = array(
    'borders' => array(
        'getAllBorders' => array(
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
            'color' => array('rgb' => '000000'),
        ),
    ),
);

$sheet->getStyle('A2:G8')->applyFromArray($styleArray);

// Add some data
$spreadsheet->createSheet();
$spreadsheet->setActiveSheetIndex(1)->setCellValue('A1', 'DAILY REPORT')->getStyle('A1')->getFont()->setBold( true )->setSize(16);
$spreadsheet->setActiveSheetIndex(1)->setCellValue('A3', 'No.')->getStyle('A3')->getFont()->setBold( true );
$spreadsheet->setActiveSheetIndex(1)->setCellValue('B3', 'Name')->getStyle('B3')->getFont()->setBold( true );
$spreadsheet->setActiveSheetIndex(1)->setCellValue('C3', 'Email')->getStyle('C3')->getFont()->setBold( true );
$spreadsheet->setActiveSheetIndex(1)->setCellValue('D3', 'Score')->getStyle('D3')->getFont()->setBold( true );
$spreadsheet->setActiveSheetIndex(1)->setCellValue('E3', 'Total time in game')->getStyle('E3')->getFont()->setBold( true );

$i=4;
$no=1;
while($r2 = mysqli_fetch_assoc($h2)) {
    $spreadsheet->getActiveSheet()->setCellValue("A$i",$no);
	$spreadsheet->getActiveSheet()->setCellValue("B$i",$r2['full_name']);
    $spreadsheet->getActiveSheet()->setCellValue("C$i",$r2['email']);
    $spreadsheet->getActiveSheet()->setCellValue("D$i",$r2['score']);
    $spreadsheet->getActiveSheet()->setCellValue("E$i",$r2['time']);
$i++;
$no++;
}

// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Daily report');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

$writer = new Xlsx($spreadsheet);
$writer->save('../report/daily-report-'.date('dmY').'.xlsx');
?>