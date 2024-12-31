<?php

require_once "./models/Donation_State_Interfaces.php";
require_once "./models/Donation_State_ProcessDonation.php";
require_once "./models/DonationModel.php";

class DonationGetDataState implements IDonationState
{    
    public function execute(Donation $donation): void
    {
        $donationData = [
            'donatorId' => $donation->getDonatorId(),
            'donationType' => $donation->getDonationType(),
            'donationAmount' => $donation->getDonationAmount(),
            'donatedItem' => $donation->getDonatedItem(),
            'paymentType' => $donation->getPaymentType()
        ];

        $result = Donation::create_new_donation($donationData);

        if ($result) {
            echo json_encode(['success' => true]);
        } 
        else {
            echo json_encode(['success' => false, 'message' => 'Failed to save donation details']);
        }    
        exit;
    }

    public function next(Donation $donation): void
    {
        // $donation->setState(new DonationStateProcess());
        $donation->setState(new DonationStateComplete());
    }

    public function previous(Donation $donation): void
    {
        // No previous states
    }
}

?>