<?php

interface IObserver
{
    public function sendnotification(string $msg);
    public function getMessage();
    public function setMessage(string $msg);
}