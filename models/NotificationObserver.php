<?php

require_once "./models/IObserver.php";

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
    private ISubject $subj;
    private string $msg;
    public function __construct(ISubject $subj)
    {
        $this->subj = $subj;
        $this->subj->attach($this);
    }

    public function sendnotification(string $msg){
        // echo "sending email $msg </br>";
        $_SESSION["notifications"] .= "Sending Email $msg </br>";
        // echo "echoing session";
        // echo $_SESSION["notifications"];
        // echo "Ending echoing";
    }
    public function setMessage(string $msg){
        $this->msg = $msg;
    }
    public function getMessage(){
        return $this->msg;
    }
}