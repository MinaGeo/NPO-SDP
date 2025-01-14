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

    public function removeDonation() 
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'removeDonation') {
            if (!empty($_POST['id'])) {
                $donationId = $_POST['id'];
                $status = Donation::remove_by_id($donationId);
                if($status){
                    header("Location: donationAdmin");
                }
            }
        }
    }

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

    // Show donation admin page
    public function showAdmin()
    {
        // Check if user is admin
        if ($_SESSION['USER_TYPE'] !== 0) {
            echo "Cannot access this page. Unauthorized user.";
            exit;
        }

        // Getting donations list 
        $donationsList = Donation::get_all();

        // Check if donations list is available
        // if (!isset($donationsList) || !is_array($donationsList)) {
        //     echo "Error: Donations list is not available.";
        //     exit;
        // }

        // Display donation admin page
        $this->donationView = new DonationView();
        $this->donationView->showAdmin($donationsList);
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
                $donationData = 
                $this->donation = new Donation([]);
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
