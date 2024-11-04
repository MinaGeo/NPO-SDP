<?php
include_once './models/Donation.php';
include_once './DonationClasses.php';
include_once './PaymentClasses.php';

class DonationController {
    private $pdo;
    private $donationModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->donationModel = new Donation($pdo);
    }

    public function processDonation($postData) {
        $donationType = $postData['donationType'];
        $donatorName = $postData['donatorName'];
        $donationAmount = isset($postData['amount']) ? (float)$postData['amount'] : 0.0;
        $donatedItem = $postData['donatedItem'] ?? '';
        $paymentType = $postData['paymentType'];

        // Initialize Donation Context based on the type
        if ($donationType === 'monetary') {
            $donationContext = new DonationContext(new MonetaryDonation($donationAmount), $donationAmount);
        } else {
            $donationContext = new DonationContext(new NonMonetaryDonation($donatedItem), 0.0, $donatedItem);
        }
        $donationContext->doDonation();

        // Initialize Payment Context based on the type
        if ($paymentType === 'paypal') {
            $paypalEmail = $postData['paypalEmail'];
            $paypalPassword = $postData['paypalPassword'];
            $paymentContext = new PaymentContext(new PayByPaypal($paypalEmail, $paypalPassword));
        } else {
            $cardNumber = $postData['cardNumber'];
            $cvv = $postData['cvv'];
            $expiryDate = $postData['expiryDate'];
            $paymentContext = new PaymentContext(new PayByCreditCard($cardNumber, $cvv, $expiryDate));
        }

        $paymentSuccess = $paymentContext->doPayment($donationAmount);

        if ($paymentSuccess) {
            $this->donationModel->saveDonation($donatorName, $donationType, $donationAmount, $donatedItem, $paymentType);
            include './views/donationSuccess.php';
        } else {
            echo "Donation or payment failed.";
        }
    }
}

