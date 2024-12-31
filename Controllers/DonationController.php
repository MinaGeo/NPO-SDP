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
    private Donation $donation;
    // Show donation page
    public function show()
    {
        $this->donationView = new DonationView();
        $this->donationView->showDonation();
    }

    public function showProcessing()
    {
        $this->donationView = new DonationView();
        $this->donationView->showProcessing();
    }

    public function showSuccess()
    {
        $this->donationView = new DonationView();
        $this->donationView->showSuccess();
    }

    // Process donation
    public function collectDonationData()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donationFlag'])) {
            // Ensure all necessary parameters are present
            if (!empty($_POST['donatorName']) && !empty($_POST['donationType']) && !empty($_POST['paymentType'])) {
                $donatorName = $_POST['donatorName'];
                $donationType = $_POST['donationType'];
                $donationAmount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0.0;
                $donatedItem = $_POST['donatedItem'] ?? '';
                $paymentType = $_POST['paymentType'];

                /* ---------------------------------------------------------------  */
                /*                    Getting Donation Data State                   */
                /* ---------------------------------------------------------------  */
                // Initialize the donation object
                $this->donation = new Donation();

                $this->donation->setDonatorId($_SESSION['USER_ID']);
                $this->donation->setDonatedItem($donatedItem);
                $this->donation->setDonationType($donationType);
                $this->donation->setDonationAmount($donationAmount);
                $this->donation->setPaymentType($paymentType);
                
                $this->donation->executeState(); 
                $this->donation->nextState();
                
                
                // $this->donation->executeState();

                /* ---------------------------------------------------------------  */
                /*                    Processing Payment State                      */
                /* ---------------------------------------------------------------  */
                // if ($this->donation->getPaymentType() === 'paypal') {
                //     $donationStrategy = new MonetaryDonation($donationAmount);
                //     $donationStrategy->setPaypalPayment($_POST['paypalEmail'], $_POST['paypalPassword']);
                //     $this->donation->setDonationStrategy($donationStrategy);
                // } 
                // else {
                //     $donationStrategy = new MonetaryDonation($donationAmount);
                //     $donationStrategy->setCreditCardPayment($_POST['cardNumber'], $_POST['cvv'], $_POST['expiryDate']);
                //     $this->donation->setDonationStrategy($donationStrategy);
                // }
                // $this->donation->executeState();

                /* ---------------------------------------------------------------  */
                /*                    Donation Completed State                      */
                /* ---------------------------------------------------------------  */

                //     if ($paymentSuccess) {
                //         // Store donation in database
                //         $donationData = [
                //             'donatorId' => $_SESSION['USER_ID'],
                //             'donationType' => $donationType,
                //             'donationAmount' => $donationAmount,
                //             'donatedItem' => $donatedItem,
                //             'paymentType' => $paymentType
                //         ];

                //         $result = Donation::create_new_donation($donationData);

                //         if ($result) {
                //             echo json_encode(['success' => true]);
                //         } 
                //         else {
                //             echo json_encode(['success' => false, 'message' => 'Failed to save donation details']);
                //         }
                //     } 
                //     else {
                //         // If payment failed
                //         echo json_encode(['success' => false, 'message' => 'Payment failed']);
                //     }
                // } 
                // else {
                //     // Missing parameters
                //     echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                // }
            }
            exit; // End the request
        }
    }

    public function executeDonationState()
    {
        $this->donation->executeState();
        $this->donation->nextState();
    }
}
