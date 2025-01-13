<?php

require_once "IEventSubject.php";


class EventSubject implements ISubject {
    private $users;
    private $msg;

    public function attach(IObserver $messagable): void
    {
        $this->users[] = $messagable;
    }

    public function detach(int $idx)
    {
        // Let's say we want to remove the element at index 2
        unset($this->users[$idx]);

        // To reindex the array after removal
        $this->users = array_values($this->users);
    }

    public function notifyUsers(): void
    {
        $size = count($this->users);
        // echo "Notifying Media Observers $size...</br>";
        foreach ($this->users as $user) {
            $user->sendNotification("Event Subscription", $this->msg);
        }
    }

    public function sendEventNotification(string $msg){
        // echo "Changing the message </br>";
        $this->msg = $msg;
        $this->notifyUsers();
    }
}