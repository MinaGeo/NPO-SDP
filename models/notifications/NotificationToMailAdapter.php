<?php

require_once "IObserver.php";
require_once "./models/phpmailer.php";

interface INotification {
    public function sendNotification(string $title, string $msg): void;
}


class NotificationToMailAdapter implements INotification {
    private MailFacade $mailFacade;

    public function __construct() {
        $this->mailFacade = new MailFacade();

    }

    public function sendNotification(string $title, string $msg): void {

        if (isset($_SESSION['USER_EMAIL']) && !empty($_SESSION['USER_EMAIL'])){
            $this->mailFacade->sendEmail(
                $_SESSION['USER_EMAIL'],
                '',
                $title,
                $msg
            );
        }
    }
}
