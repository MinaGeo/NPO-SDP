<?php

require_once "./models/donations/Donation_State_Interfaces.php";
require_once "./models/donations/Donation_State_Complete.php";
require_once "./models/donations/Donation_State_GetData.php";
require_once "./models/donations/DonationClasses.php";
require_once "./models/donations/DonationModel.php";

class DonationStateProcess implements IDonationState
{    
    public function execute(Donation $donation): void
    {
        if($donation->getDonationType() === 'monetary'){
            $donation->setDonationStrategy(new MonetaryDonation());
        }
        else{
            $donation->setDonationStrategy(new NonMonetaryDonation());
        }
        $donation->processDonation();
    }

    public function next(Donation $donation): void
    {
        $donation->setState(new DonationStateComplete());
    }

    public function previous(Donation $donation): void
    {
        $donation->setState(new DonationGetDataState());
    }
}

?>