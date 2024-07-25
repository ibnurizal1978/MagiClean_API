<?php
ini_set('display_errors',1);  error_reporting(E_ALL);
set_include_path('/var/www/html/cms/');
require '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$db_server        = '127.0.0.1';
$db_user          = 'root';
$db_password      = 'Mag1clean@888';
$db_name          = 'db_magiclean';
$conn 			  = new mysqli($db_server,$db_user,$db_password,$db_name) or die (mysqli_error($conn));

$s1 = "SELECT date_format(created_at, '%d-%m-%Y') as tgl, count(user_id) as total FROM tbl_user where created_at >= DATE(NOW()) - INTERVAL 7 DAY group by created_at";
$h1 = mysqli_query($conn, $s1) or die (mysqli_error($conn));

$s2 = "SELECT date_format(created_at, '%d-%m-%Y') as tgl, count(email) as total FROM tbl_leaderboard where created_at >= DATE(NOW()) - INTERVAL 7 DAY group by created_at;";
$h2 = mysqli_query($conn, $s2) or die (mysqli_error($conn));

$spreadsheet = new Spreadsheet();
$sheet =  $spreadsheet->createSheet();
//$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Sign up report');
$sheet->setCellValue('A1', 'Weekly Report')->getStyle('A1')->getFont()->setBold( true )->setSize(16);
$sheet->setCellValue('A3', 'Date')->getStyle('A3')->getFont()->setBold( true );
$sheet->setCellValue('B3', 'No. of Sign Up')->getStyle('B3')->getFont()->setBold( true );
$i=4;
while($r1 = mysqli_fetch_assoc($h1)) {
    $sheet->setCellValue("A$i",$r1['tgl']);
    $sheet->setCellValue("B$i",$r1['total']);
$i++;
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
$spreadsheet->setActiveSheetIndex(1)->setCellValue('A1', 'Weekly Report')->getStyle('A1')->getFont()->setBold( true )->setSize(16);
$spreadsheet->setActiveSheetIndex(1)->setCellValue('A3', 'Date')->getStyle('A3')->getFont()->setBold( true );
$spreadsheet->setActiveSheetIndex(1)->setCellValue('B3', 'No. of users played')->getStyle('B3')->getFont()->setBold( true );

$i=4;
$no=1;
while($r2 = mysqli_fetch_assoc($h2)) {
    $spreadsheet->getActiveSheet()->setCellValue("A$i",$r2['tgl']);
	$spreadsheet->getActiveSheet()->setCellValue("B$i",$r2['total']);
$i++;
$no++;
}

// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('No. of players who played');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

$writer = new Xlsx($spreadsheet);
$writer->save('../report/weekly-report-'.date('dmY').'.xlsx');
?>