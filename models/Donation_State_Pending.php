<?php

require_once "./models/DonationModel.php";
require_once "./models/Donation_State_Interfaces.php";
require_once "./models/Donation_State_Processing.php";

// Concrete state class: Pending
// This is the first state in the state machine => no previous state
class DonationStatePending implements IDonationState
{

    public function previous(Donation $donation)
    {
        // No previous state
    }

    public function execute()
    {
        echo "Donation is pending. <br>";
    }

    public function next(Donation $donation)
    {
        $donation->setState(new DonationStateProcessing());
    }
}

?>