<?php
ini_set('display_errors',1);  error_reporting(E_ALL);
$file = "../report/daily-report-".date('dmY').".xlsx";
/*
$to = 'myself@localhost'; 
$from = 'noreply@localhost'; 
$fromName = 'ggg'; 
$subject = 'PHP Email';
$htmlContent = "Daily report for date: ".date("M d, Y");
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
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require '../plugins/PHPMailer/src/Exception.php';
require '../plugins/PHPMailer/src/PHPMailer.php';
require '../plugins/PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);
$mail->SMTPDebug = 2;
$mail->isSMTP();    
$mail->Mailer     = "smtp";   
$mail->Host       = 'mail.magicleanfortunetown.com';
$mail->SMTPAuth   = true;       
$mail->Username   = 'noreply@magicleanfortunetown.com';
$mail->Password   = 'Noreply$123';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;  
$mail->Port       = 587;            
$mail->SMTPSecure = 'tls';
$mail->SMTPAutoTLS = false;
$mail->SMTPKeepAlive = true;
$mail->setFrom('noreply@magicleanfortunetown.com', 'Magiclean Admin');
$mail->addAddress('ibnurizal@gmail.com');
$mail->addAttachment($file);
$mail->isHTML(true); 
$mail->Subject = 'Weekly Report';
$mail->AddAttachment($file); 
$mail->Body    = 'Daily report';

if(!$mail->send()) {
    echo 'Message was not sent.';
    echo 'Mailer error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}
 ?>