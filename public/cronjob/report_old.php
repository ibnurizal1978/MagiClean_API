<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require '../plugins/PHPMailer/src/Exception.php';
require '../plugins/PHPMailer/src/PHPMailer.php';
require '../plugins/PHPMailer/src/SMTP.php';

$db_server        = '127.0.0.1';
$db_user          = 'root';
$db_password      = '';
$db_name          = '';
$conn 			  = new mysqli($db_server,$db_user,$db_password,$db_name) or die (mysqli_error($conn));

require_once '../plugins/PHPExcel.php';
require_once '../plugins/PHPExcel/IOFactory.php';
$result = mysqli_query($conn,"SELECT * FROM tbl_user");
$result1 = mysqli_query($conn,"SELECT *,data_format(created_at, '%d-%m-%Y') as created_at FROM tbl_leaderboard a INNER JOIN tbl_user b USING (email)");
/* Create new PHPExcel object*/
$objPHPExcel = new PHPExcel();

/* Create a first sheet, representing sales data*/
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Name');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Email');
$i=2;
while($row = mysqli_fetch_array($result)) {
	$name=$row['name'];
	$email=$row['email'];
	$objPHPExcel->getActiveSheet()->setCellValue("A$i",$name);
	$objPHPExcel->getActiveSheet()->setCellValue("B$i",$email);
$i++;
}



/*Rename sheet*/
$objPHPExcel->getActiveSheet()->setTitle('Emplyoee profile');

/* Create a new worksheet, after the default sheet*/
$objPHPExcel->createSheet();

/* Add some data to the second sheet, resembling some different data types*/
$objPHPExcel->setActiveSheetIndex(1);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Name');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Email');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Score');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Total time in game');
$i=2;
while($r1= mysqli_fetch_array($result1)) {
	//$salary=$row1['salary'];
	$objPHPExcel->getActiveSheet()->setCellValue("A$i",$r1['full_name']);
    $objPHPExcel->getActiveSheet()->setCellValue("B$i",$r1['email']);
    $objPHPExcel->getActiveSheet()->setCellValue("C$i",$r1['score']);
    $objPHPExcel->getActiveSheet()->setCellValue("D$i",$r1['time']);
$i++;
}

/* Rename 2nd sheet*/
$objPHPExcel->getActiveSheet()->setTitle('Emplyoee Salary');

/* Redirect output to a client’s web browser (Excel5)*/
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="name_of_file.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
//file_put_contents($objWriter);


/*$to = 'myself@localhost'; 
$from = 'noreply@localhost'; 
$fromName = 'ggg'; 
$subject = 'PHP Email';
//$file = "../file/".$file_name.".pdf";
$htmlContent = $content;
$headers = "From: $fromName"." <".$from.">"; 
$semi_rand = md5(time());  
$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";  
$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";  
$message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" . 
"Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n";  

    // Preparing attachment 
    if(!empty($file) > 0){ 
        if(is_file($file)){ 
            $message .= "--{$mime_boundary}\n"; 
            $fp =    @fopen($file,"rb"); 
            $data =  @fread($fp,filesize($file)); 
    
            @fclose($fp); 
            $data = chunk_split(base64_encode($data)); 
            $message .= "Content-Type: application/octet-stream; name=\"".basename($file)."\"\n" .  
            "Content-Description: ".basename($file)."\n" . 
            "Content-Disposition: attachment;\n" . " filename=\"".basename($file)."\"; size=".filesize($file).";\n" .  
            "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n"; 
        } 
    }

$message .= "--{$mime_boundary}--"; 
$returnpath = "-f" . $from; 
$mail = @mail($to, $subject, $message, $headers, $returnpath);  
echo $mail?"<h1>Email Sent Successfully!</h1>":"<h1>Email sending failed.</h1>"; 
*/

/*
 $mail = new PHPMailer(true);
 $mail->isSMTP();    
 $mail->Mailer     = "smtp";   
 $mail->Host       = '';                      //'smtp.office365.com'
 $mail->SMTPAuth   = true;       
 $mail->Username   = 'afd@assisihospice.org.sg';                     //afd@assisihospice.org.sg
 $mail->Password   = 'Muc36340';                               //Muc36340
 $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;  
 $mail->Port       = 587;            
 $mail->SMTPSecure = 'tls';
 $mail->SMTPAutoTLS = false;
 $mail->SMTPKeepAlive = true;
 $mail->setFrom('afd@assisihospice.org.sg', 'Assisi Funday (no reply)'); //'afd@assisihospice.org.sg', 'Assisi Funday (no reply)'
 $mail->addAddress($rowx['cart_email'], $rowx['cart_name']);
// if($ada == 1) { $mail->addAttachment($file); }
 $mail->isHTML(true); 
 $mail->Subject = 'Your Assisi Order';
 $mail->Body    = $content;

 if(!$mail->send()) {
     echo 'Message was not sent.';
     echo 'Mailer error: ' . $mail->ErrorInfo;
     $email_msg = 'Message was not sent.'.$mail->ErrorInfo;;
 } else {
     echo 'Message has been sent';
 }*/
 ?>