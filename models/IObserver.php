<?php

interface IObserver
{
    public function sendnotification(string $msg);
    public function getFirstName();
}