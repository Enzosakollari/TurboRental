<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';
require __DIR__ . '/../PHPMailer/src/Exception.php';


function sendEmail($text, $emailDestination){

    $mail = new PHPMailer(true);
    try {
        
        $mail->SMTPDebug = false;                      
        $mail->isSMTP();                                            
        $mail->Host = 'smtp.gmail.com';                     
        $mail->SMTPAuth = true;                                   
        $mail->Username = 'rentmakina@gmail.com';                     
        $mail->Password = 'zahn jkma ryxn eknv';                               
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            
        $mail->Port = 587;                                    
        $mail->setFrom('rentmakina@gmail.com', 'Turbo Rentals');
        $mail->addAddress($emailDestination);     

        
        


        
        $mail->isHTML(true);                                  
        $mail->Subject = 'Verification Code';
        $mail->Body = $text;

        if ($mail->send()) {
            return true; 
        } else {
            $errorMessage = $mail->ErrorInfo ?: 'Unknown mailer error';
            error_log("Email sending failed: " . $errorMessage); 
            return $errorMessage; 
        }
    } catch (Exception $e) {
        
        $errorMessage = $mail->ErrorInfo ?: $e->getMessage();
        error_log("Mailer Error: {$errorMessage}"); 
        
        return $errorMessage;
    }
}
?>
