<?php
require_once 'DonationTemplate.php';
require_once 'DonationModel.php';
require_once 'models/NotificationToMailAdapter.php';

class MonetaryDonationProcessor extends DonationTemplate
{
    private DonationContext $donationContext;
    private float $donationAmount;
    private string $paymentType, $donatorName, $donationType;

    public function __construct(string $donatorName, string $donationType, float $donationAmount, string $paymentType)
    {
        $this->donationAmount = $donationAmount;
        $this->paymentType = $paymentType;
        $this->donatorName = $donatorName;
        $this->donationType = $donationType;
        $this->donationContext = new DonationContext(new MonetaryDonation($donationAmount), $donationAmount);
    }

    protected function processPayment()
    {
        $this->donationContext->doDonation();
        // Initialize the payment context based on the payment type
        if ($this->paymentType === 'paypal') {
            $paypalEmail = $_POST['paypalEmail'];
            $paypalPassword = $_POST['paypalPassword'];
            $paymentContext = new PaymentContext(new PayByPaypal($paypalEmail, $paypalPassword));
        } else {
            $cardNumber = $_POST['cardNumber'];
            $cvv = $_POST['cvv'];
            $expiryDate = $_POST['expiryDate'];
            $paymentContext = new PaymentContext(new PayByCreditCard($cardNumber, $cvv, $expiryDate));
        }


        // Attempt payment processing
        $paymentSuccess = $paymentContext->doPayment($this->donationAmount);

        if ($paymentSuccess) {
            $result = Donation::saveDonation($this->donatorName, $this->donationType, $this->donationAmount, '', $this->paymentType);
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save donation details']);
            }
        } else {
            // If payment failed
            echo json_encode(['success' => false, 'message' => 'Payment failed']);
        }
    }

    protected function sendReceipt(string $donatorName)
    {
        $email = new NotificationToMailAdapter();
        $email->sendNotification("Monetary Donation!", $donatorName . ": has donated " . $this->donationAmount);
    }
}
