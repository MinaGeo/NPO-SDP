<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require '../vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    // $mail->isMail();                                            //Send using SMTP
    $mail->isSMTP();                                            //Send using SMTP
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Host       = 'smtp.gmail.com';                    //Set the SMTP server to send through
    $mail->Username   = 'sdp.charity.project@gmail.com';     //SMTP username
    $mail->Password   = 'roui mioi opoy joye';               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;      //Enable TLS encryption
    $mail->Port       = 587;                                 //TCP port to connect to

    //Recipients
    $mail->setFrom('sdp.charity.project@gmail.com');
    $mail->addAddress('awael92@gmail.com');     //Add a recipient
    $mail->addReplyTo('sdp.charity.project@gmail.com');

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

?>