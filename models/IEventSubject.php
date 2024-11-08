    <?php

require_once "./models/IMessagble.php";
    interface IObservable
    {
        // Attach an observer to the subject.
        public function attach(IMEssagable $messagable);

        // Detach an observer from the subject.
        public function detach(int $idx);

        // Notify all observers about an event.
        public function notifyUsers(string $msg);
    }
