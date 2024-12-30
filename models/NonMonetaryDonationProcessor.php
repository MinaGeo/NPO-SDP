<?php
require_once 'DonationTemplate.php';

class NonMonetaryDonationProcessor extends DonationTemplate
{
    private DonationContext $donationContext;

    public function __construct(string $donatedItem) {
        $this->donationContext = new DonationContext(new NonMonetaryDonation($donatedItem), 0.0, $donatedItem);
    }

    protected function processPayment()
    {
        $this->donationContext->doDonation();
    }

    protected function sendReceipt()
    {
        // Send receipt for non-monetary donation
        echo "Sending receipt for non-monetary donation...\n";
    }
}
?>