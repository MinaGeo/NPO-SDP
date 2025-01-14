<?php

require_once "./models/donations/DonationModel.php";
require_once "./models/donations/Donation_State_Interfaces.php";
require_once "./models/donations/Donation_State_ProcessDonation.php";
require_once "./models/donations/Donation_State_GetData.php";
require_once "./models/phpmailer.php";
require_once "./models/userBase.php";

// Concrete state class: Complete
class DonationStateComplete implements IDonationState
{
    public function previous(Donation $donation)
    {
        $donation->setState(new DonationStateProcess());
    }

    public function execute(Donation $donation)
    {
        $user = User::get_by_id($_SESSION['USER_ID']);
        $username = $user->getFirstName() . ' ' . $user->getLastName();

        // HTML email content for PHPMailer
        $emailContent = <<<EOD
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    margin: 0;
                    padding: 0;
                    background-color: #f4f4f4;
                }
                .email-container {
                    max-width: 600px;
                    margin: 20px auto;
                    background: #ffffff;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    overflow: hidden;
                }
                .email-header {
                    background: #148a74;
                    color: #ffffff;
                    text-align: center;
                    padding: 20px 10px;
                }
                .email-body {
                    padding: 20px 30px;
                }
                .email-footer {
                    text-align: center;
                    padding: 10px;
                    font-size: 12px;
                    color: #666;
                    background: #f4f4f4;
                }
                .btn {
                    display: inline-block;
                    padding: 10px 20px;
                    margin-top: 20px;
                    background-color: #148a74;
                    color: #ffffff;
                    text-decoration: none;
                    border-radius: 4px;
                }
                .btn:hover {
                    background-color:rgb(12, 105, 88);
                }
            </style>
        </head>
        <body>
            <div class="email-container">
                <div class="email-header">
                    <h1>Thank You for Your Support!</h1>
                </div>
                <div class="email-body">
                    <p>Dear {$username} ,</p>
                    <p>On behalf of everyone at SDP Project, we sincerely thank you for your generous donation. Your support helps us continue our mission and make a meaningful impact in our community.</p>
                    <p>Your contribution is not just a donation; it's an investment in the future we are working to create together. We are incredibly grateful for your trust and belief in our cause.</p>
                    <p>If you have any questions or would like to learn more about how your donation is making a difference, please don't hesitate to contact us.</p>
                    <p>Thank you once again for being an essential part of our journey.</p>
                    <a href="https://www.youtube.com/watch?v=xvFZjo5PgG0" style="color:rgb(255, 255, 255);" class="btn">Learn More</a>
                </div>
                <div class="email-footer">
                    <p>SDP Project, 1 El Sarayat St.، ABBASSEYA, El Weili, Cairo Governorate 11535</p>
                    <p><a href="https://www.youtube.com/watch?v=giTIHf4Nxvw" style="color: #148a74;">Visit our website</a> | <a href="mailto:sdp.charity.project@gmail.com" style="color: #148a74;">Contact us</a></p>
                </div>
            </div>
        </body>
        </html>
        EOD;

        // Send email to donator
        $mailFacade = new MailFacade();
        $mailFacade->sendEmail(
            $_SESSION['USER_EMAIL'],
            // './assets/thank you.jpg',
            '',
            'Donation Complete',
            $emailContent
        );

        exit;
    }

    public function next(Donation $donation)
    {
        // Reset to the first state
        $donation->setState(new DonationGetDataState());
    }
}
