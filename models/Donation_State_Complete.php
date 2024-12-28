<?php

require_once "./models/DonationModel.php";
require_once "./models/Donation_State_Interfaces.php";
require_once "./models/Donation_State_Processing.php";

// Concrete state class: Complete
class DonationStateComplete implements IDonationState
{
    public function previous(Donation $donation)
    {
        $donation->setState(new DonationStateProcessing());
    }

    public function execute()
    {
        echo "Donation is complete. <br>";
    }

    public function next(Donation $donation)
    {
        // No next state
    }
}

?>