<?php
require_once 'DonationTemplate.php';

class NonMonetaryDonationProcessor extends DonationTemplate
{
    private Donation $donation;

    public function __construct(Donation $donation)
    {
        $this->donation = $donation;
    }

    protected function processPayment()
    {
        DonationView::nonMonetaryPopUp($this->donation);
    }

    protected function sendReceipt(string $donatorName)
    {
        $email = new NotificationToMailAdapter();
        $email->sendNotification("Non-Monetary Donation!",$donatorName . ": has donated ". $this->donation->getDonatedItem() . ". We will collect it soon!");
    }
}
?>