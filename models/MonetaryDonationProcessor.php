<?php
require_once 'DonationTemplate.php';
require_once 'models/DonationModel.php';

class MonetaryDonationProcessor extends DonationTemplate
{
    private DonationContext $donationContext;

    public function __construct(float $donationAmount) {
        $this->donationContext = new DonationContext(new MonetaryDonation($donationAmount), $donationAmount);
    }

    protected function processPayment()
    {
        $this->donationContext->doDonation();
    }

    protected function sendReceipt()
    {
        // Send receipt for monetary donation
        echo "Sending receipt for monetary donation...\n";
    }
}
?>