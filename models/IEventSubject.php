    <?php

    interface SplSubject
    {
        // Attach an observer to the subject.
        public function attach(SplObserver $observer);

        // Detach an observer from the subject.
        public function detach(SplObserver $observer);

        // Notify all observers about an event.
        public function notify();
    }
