<?php
// Rename the interfaces to avoid conflict with PHP's built-in SplSubject and SplObserver
interface CustomSubject
{
    public function attach(CustomObserver $observer);
    public function detach(CustomObserver $observer);
    public function notify();
}

class Subject implements CustomSubject
{
    public $state;
    private $observers;

    public function __construct()
    {
        $this->observers = new \SplObjectStorage();
    }

    public function attach(CustomObserver $observer): void
    {
        echo "Subject: Attached an observer.</br>";
        $this->observers->attach($observer);
    }

    public function detach(CustomObserver $observer): void
    {
        $this->observers->detach($observer);
        echo "Subject: Detached an observer.</br>";
    }

    public function notify(): void
    {
        echo "Subject: Notifying observers...</br>";
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    public function someBusinessLogic(): void
    {
        echo "</br>Subject: I'm doing something important.</br>";
        $this->state = rand(0, 10);
        echo "Subject: My state has just changed to: {$this->state}</br>";
        $this->notify();
    }
}

interface CustomObserver
{
    public function update(CustomSubject $subject);
}

class ConcreteObserverA implements CustomObserver
{
    public function update(CustomSubject $subject): void
    {
        if ($subject->state < 3) {
            echo "ConcreteObserverA: Reacted to the event.</br>";
        }
    }
}

class ConcreteObserverB implements CustomObserver
{
    public function update(CustomSubject $subject): void
    {
        if ($subject->state == 0 || $subject->state >= 2) {
            echo "ConcreteObserverB: Reacted to the event.</br>";
        }
    }
}

// Testing the observer pattern
$subject = new Subject();

$o1 = new ConcreteObserverA();
$subject->attach($o1);

$o2 = new ConcreteObserverB();
$subject->attach($o2);

$subject->someBusinessLogic();
$subject->someBusinessLogic();

$subject->detach($o2);

$subject->someBusinessLogic();
