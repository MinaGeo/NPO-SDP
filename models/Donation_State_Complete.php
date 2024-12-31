<?php

require_once "./models/DonationModel.php";
require_once "./models/Donation_State_Interfaces.php";
require_once "./models/Donation_State_ProcessDonation.php";
require_once "./models/Donation_State_GetData.php";
require_once "./models/phpmailer.php";

// Concrete state class: Complete
class DonationStateComplete implements IDonationState
{
    public function previous(Donation $donation)
    {
        $donation->setState(new DonationStateProcess());
    }

    public function execute(Donation $donation)
    {
        // Send email to donator
        $mailFacade = new MailFacade();
        $mailFacade->sendEmail(
            $_SESSION['USER_EMAIL'],
            '/assets/homepage-image.jpg',
            'Donation Complete',
            'Thank you for your donation'
        );
        // echo "Donation is complete. <br>";
        echo json_encode(['success' => true]);
        exit;
    }

    public function next(Donation $donation)
    {
        // Reset to the first state
        $donation->setState(new DonationGetDataState());
    }
}

?>