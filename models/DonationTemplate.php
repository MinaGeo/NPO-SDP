<?php

abstract class DonationTemplate
{
    // Template Design Pattern
    public function processDonation()
    {
        $this->processPayment();
        $this->sendReceipt();
    }

    // Abstract methods
    abstract protected function processPayment();
    abstract protected function sendReceipt();
}
?>