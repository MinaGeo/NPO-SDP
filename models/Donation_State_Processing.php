<?php

require_once "./models/DonationModel.php";
require_once "./models/Donation_State_Interfaces.php";
require_once "./models/Donation_State_Pending.php";
require_once "./models/Donation_State_Complete.php";

// Concrete state class: Processing
class DonationStateProcessing implements IDonationState
{
    public function previous(Donation $donation)
    {
        $donation->setState(new DonationStatePending());
    }

    public function execute()
    {
        echo "Donation is being processed. <br>";
    }

    public function next(Donation $donation)
    {
        $donation->setState(new DonationStateComplete());
    }
}

?>