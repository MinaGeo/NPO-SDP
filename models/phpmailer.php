<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require_once './vendor/autoload.php';

class MailFacade{
    private static $host;
    private static $port;
    private static $username;
    private static $password;
    private static $fromMail;
    private static $fromName;

    public function __construct() {
        // Mail Data
        self::$host = 'smtp.gmail.com';
        self::$port = 587;
        self::$username = 'sdp.charity.project@gmail.com';
        self::$password = 'roui mioi opoy joye';
        self::$fromMail = 'sdp.charity.project@gmail.com';
        self::$fromName = 'SDP Charity Project';
    }

    public function sendEmail($toMailAddress,$attachmentPath,$subject,$body) 
    {
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings                                           //Send using SMTP
            $mail->isSMTP();                                            //Send using SMTP
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Host       = self::$host;                    //Set the SMTP server to send through
            $mail->Username   = self::$username;     //SMTP username
            $mail->Password   = self::$password;               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;      //Enable TLS encryption
            $mail->Port       = self::$port;                                 //TCP port to connect to

            //Recipients
            $mail->setFrom(self::$fromMail, self::$fromName);
            $mail->addAddress($toMailAddress);     //Add a recipient
            $mail->addReplyTo(self::$fromMail, self::$fromName);

            //Attachments
            if($attachmentPath != '' && $attachmentPath != null && file_exists($attachmentPath)){
                $mail->addAttachment($attachmentPath);         //Add attachments
            }

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = $body;

            $mail->send();
        } catch (Exception $e) {
            // rethrow;
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}

// Unit testing
// $mailFacade = new MailFacade();
// $mailFacade->sendEmail('awael92@gmail.com', '/assets/rotaract.png', 'SDP Notification', 'This is a test email');

?>