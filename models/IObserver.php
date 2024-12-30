<?php

interface IObserver
{
    public function sendNotification(string $title, string $msg);
    public function getMessage();
    public function setMessage(string $msg);
}