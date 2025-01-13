<?php
// Include necessary files and configurations
require_once './models/donations/DonationModel.php';
require_once './models/donations/DonationClasses.php';
require_once './models/PaymentClasses.php';
require_once './views/DonationView.php';
require_once './models/donations/MonetaryDonationProcessor.php';
require_once './models/donations/NonMonetaryDonationProcessor.php';

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
                if ($donationType === 'monetary') {
                    $processor = new MonetaryDonationProcessor($donatorName, $donationType, $donationAmount, $paymentType);
                } else {
                    $processor = new NonMonetaryDonationProcessor($donatorName, $donationType, $donatedItem);
                }
    
                // Process the donation
                $processor->processDonation($donatorName);

            } else {
                // Missing parameters
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            }
        }
        exit; // End the request
    }
    
}
?>
