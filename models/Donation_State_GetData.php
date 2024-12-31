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
        echo json_encode(['success' => $result]);
    
        // exit;
    }

    public function next(Donation $donation): void
    {
        // $donation->setState(new DonationStateProcess());
        $donation->setState(new DonationStateProcess());
    }

    public function previous(Donation $donation): void
    {
        // No previous states
    }
}

?>