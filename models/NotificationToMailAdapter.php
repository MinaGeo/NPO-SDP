<?php

require_once "./models/IObserver.php";
require_once "./models/phpmailer.php";

interface INotification {
    public function sendNotification(string $msg): void;
}


class NotificationToMailAdapter implements INotification {
    private MailFacade $mailFacade;

    public function __construct() {
        $this->mailFacade = new MailFacade();

    }

    public function sendNotification(string $msg): void {
        // Delegate email notification to EMAILObserver
        $this->mailFacade->sendEmail(
            $_SESSION['USER_EMAIL'],
            '',
            'Event Notification',
            $msg
        );
    }
}
