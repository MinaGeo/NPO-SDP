    <?php

    require_once "./models/IObserver.php";
    interface ISubject
    {
        // Attach an observer to the subject.
        public function attach(IObserver $messagable);

        // Detach an observer from the subject.
        public function detach(int $idx);

        // Notify all observers about an event.
        public function notifyUsers(string $msg);
    }
