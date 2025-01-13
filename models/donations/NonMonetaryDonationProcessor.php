<?php
require_once 'DonationTemplate.php';

class NonMonetaryDonationProcessor extends DonationTemplate
{
    private DonationContext $donationContext;
    private string $donatedItem;
    private string $donatorName, $donationType;

    public function __construct(string $donatorName, string $donationType, string $donatedItem) {
        $this->donatedItem = $donatedItem;
        $this->donatorName = $donatorName;
        $this->donationType = $donationType;
        $this->donationContext = new DonationContext(new NonMonetaryDonation($donatedItem), 0.0, $donatedItem);
    }

    protected function processPayment()
    {
        $this->donationContext->doDonation();
        $result = Donation::saveDonation($this->donatorName, $this->donationType, 0.0, $this->donatedItem, '');
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save donation details']);
        }
    }

    protected function sendReceipt(string $donatorName)
    {
        $email = new NotificationToMailAdapter();
        $email->sendNotification("Non-Monetary Donation!",$donatorName . ": has donated ". $this->donatedItem . ". We will collect it soon!");
    }
}
?>