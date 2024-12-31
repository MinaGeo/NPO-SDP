<?php
// Include necessary files and configurations
require_once './models/DonationModel.php';
require_once './models/DonationClasses.php';
// require_once './models/PaymentClasses.php';
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
                $cardNumber = $_POST['cardNumber'] ?? '';
                $cvv = $_POST['cvv'] ?? '';
                $expiryDate = $_POST['expiryDate'] ?? '';
                $paypalEmail = $_POST['paypalEmail'] ?? '';
                $paypalPassword = $_POST['paypalPassword'] ?? '';
                // $cardNumber

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
                $this->donation->setCardNumber($cardNumber);
                $this->donation->setCvv($cvv);
                $this->donation->setExpiryDate($expiryDate);
                $this->donation->setPaypalEmail($paypalEmail);
                $this->donation->setPaypalPassword($paypalPassword);
                // Executing first state
                $this->donation->executeState(); 

                /* ---------------------------------------------------------------  */
                /*                    Processing Donation State                     */
                /* ---------------------------------------------------------------  */
                $this->donation->nextState();
                $this->donation->executeState(); 

                /* ---------------------------------------------------------------  */
                /*                          Complete State                          */
                /* ---------------------------------------------------------------  */
                $this->donation->nextState();
                $this->donation->executeState(); 

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
