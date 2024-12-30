<?php

abstract class DonationTemplate
{
    // Template Design Pattern
    public function processDonation(string $donatorName)
    {
        $this->processPayment();
        $this->sendReceipt($donatorName);
    }

    // Abstract methods
    abstract protected function processPayment();
    abstract protected function sendReceipt(string $donatorName);
}
?>