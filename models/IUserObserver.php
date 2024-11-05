 <?php
    require_once "./IEventSubject.php";
    interface SplObserver
    {
        public function update(SplSubject $subject);
    }
