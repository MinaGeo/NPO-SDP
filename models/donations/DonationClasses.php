<?php

require_once "./models/PaymentClasses.php";
require_once "./models/donations/DonationModel.php";
require_once "./models/donations/MonetaryDonationProcessor.php";
require_once "./models/donations/NonMonetaryDonationProcessor.php";
require_once "./models/userBase.php";

// Define the IDonateStrategy interface for donation strategies
interface IDonateStrategy {
    public function processDonation(Donation $donation): void;
}
function processTemplate($donationTemplate)
{
    if(empty($_SESSION['USER_ID']) || $_SESSION['USER_ID']=='')
    {
        $user = 'Guest';
    }
    else
    {
        $user = User::get_by_id($_SESSION['USER_ID'])->getFirstName();
    }
    $donationTemplate->processDonation($user);
}
// MonetaryDonation class for monetary donations, implementing IDonateStrategy
class MonetaryDonation implements IDonateStrategy {
    private IPay $paymentStrategy;
    private String $cardNumber;
    private String $cvv;
    private String $expiryDate;
    private String $paypalEmail;
    private String $paypalPassword;

    public function setPaymentStrategy(IPay $paymentStrategy): void {
        $this->paymentStrategy = $paymentStrategy;
    }

    public function setPaypalPayment(String $paypalEmail, String $paypalPassword): void {
        $this->paypalEmail = $paypalEmail;
        $this->paypalPassword = $paypalPassword;
        $this->paymentStrategy = new PayByPaypal($this->paypalEmail, $this->paypalPassword);
    }

    public function setCreditCardPayment(String $cardNumber, String $cvv, String $expiryDate): void {
        $this->cardNumber = $cardNumber;
        $this->cvv = $cvv;
        $this->expiryDate = $expiryDate;
        $this->paymentStrategy = new PayByCreditCard($this->cardNumber, $this->cvv, $this->expiryDate);
    }

    public function processDonation(Donation $donation): void {
        if($donation->getPaymentType() === 'paypal'){
            $this->setPaypalPayment($donation->getPaypalEmail(), $donation->getPaypalPassword());
        } 
        else if($donation->getPaymentType() === 'creditCard'){
            $this->setCreditCardPayment($donation->getCardNumber(), $donation->getCvv(), $donation->getExpiryDate());
        }
        $donationTemplate = new MonetaryDonationProcessor($donation, $this->paymentStrategy);
        processTemplate($donationTemplate);
    }
}

// NonMonetaryDonation class for non-monetary donations, implementing IDonateStrategy
class NonMonetaryDonation implements IDonateStrategy {
    public function processDonation(Donation $donation): void {
        $donationTemplate = new NonMonetaryDonationProcessor($donation);
        processTemplate($donationTemplate);
    }
}


?>
