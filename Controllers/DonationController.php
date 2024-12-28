<?php
// Include necessary files and configurations
require_once './models/DonationModel.php';
require_once './models/DonationClasses.php';
require_once './models/PaymentClasses.php';
require_once './views/DonationView.php';

$configs = require "server-configs.php";

class DonationController implements IControl
{
    private $donationView;
    // Show donation page
    public function show()
    {
        $this->donationView = new DonationView();
        $this->donationView->showDonation();
    }

    // Process donation
    public function processDonation()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donationFlag'])) {
            // Ensure all necessary parameters are present
            if (!empty($_POST['donatorName']) && !empty($_POST['donationType']) && !empty($_POST['paymentType'])) {
                $donatorName = $_POST['donatorName'];
                $donationType = $_POST['donationType'];
                $donationAmount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0.0;
                $donatedItem = $_POST['donatedItem'] ?? '';
                $paymentType = $_POST['paymentType'];
    
                // Initialize the donation context based on the donation type
                // if ($donationType === 'monetary') {
                //     // $donationContext = new DonationContext(new MonetaryDonation($donationAmount), $donationAmount);
                // } else {
                //     $paymentType = '';
                //     $donationContext = new DonationContext(new NonMonetaryDonation($donatedItem), 0.0, $donatedItem);
                // }
    
                // Process the donation
                // $donationContext->doDonation();

                // Initialize the payment context based on the payment type
                if ($paymentType === 'paypal') {
                    $paypalEmail = $_POST['paypalEmail'];
                    $paypalPassword = $_POST['paypalPassword'];
                    $paymentContext = new PaymentContext(new PayByPaypal($paypalEmail, $paypalPassword));
                } 
                else {
                    $cardNumber = $_POST['cardNumber'];
                    $cvv = $_POST['cvv'];
                    $expiryDate = $_POST['expiryDate'];
                    $paymentContext = new PaymentContext(new PayByCreditCard($cardNumber, $cvv, $expiryDate));
                }
                
                // Attempt payment processing
                $paymentSuccess = $paymentContext->doPayment($donationAmount);
    
                if ($paymentSuccess) {
                    // Store donation in database
                    $donationData = [
                        'donatorId' => $_SESSION['USER_ID'],
                        'donationType' => $donationType,
                        'donationAmount' => $donationAmount,
                        'donatedItem' => $donatedItem,
                        'paymentType' => $paymentType
                    ];
                    $result = Donation::create_new_donation($donationData);
                    if ($result) {
                        echo json_encode(['success' => true]);
                    } 
                    else {
                        echo json_encode(['success' => false, 'message' => 'Failed to save donation details']);
                    }
                } 
                else {
                    // If payment failed
                    echo json_encode(['success' => false, 'message' => 'Payment failed']);
                }
            } 
            else {
                // Missing parameters
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            }
        }
        exit; // End the request
    }
}
?>
