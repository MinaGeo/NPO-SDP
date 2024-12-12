<?php

require_once "./models/IObserver.php";
require_once "./models/phpmailer.php";

class SMSObserver implements IObserver{
    private ISubject $subj;
    private string $msg;
    public function __construct(ISubject $subj)
    {
        $this->subj = $subj;
        $this->subj->attach($this);
    }

    public function sendnotification(string $msg){
        // echo "sending sms $msg </br>";
        $_SESSION["notifications"] .= "Sending SMS $msg </br>";

    }
    public function setMessage(string $msg){
        $this->msg = $msg;
    }
    public function getMessage(){
        return $this->msg;
    }
}

class EMAILObserver implements IObserver{
    // Attributes 
    private ISubject $subj;
    private string $msg;
    private MailFacade $mailFacade;

    public function __construct(ISubject $subj)
    {
        $this->mailFacade = new MailFacade();
        $this->subj = $subj;
        $this->subj->attach($this);
    }

    public function sendnotification(string $msg){
        $_SESSION["notifications"] .= "Sending Email $msg </br>";
        $this->mailFacade->sendEmail(
            $_SESSION['USER_EMAIL'],
            '',
            'Event Notification',
            $msg
        );
    }

    public function setMessage(string $msg){
        $this->msg = $msg;
    }
    public function getMessage(){
        return $this->msg;
    }
}