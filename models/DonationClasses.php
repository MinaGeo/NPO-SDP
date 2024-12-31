<?php

require_once "./models/PaymentClasses.php";
require_once "./models/DonationModel.php";

// Define the IDonateStrategy interface for donation strategies
interface IDonateStrategy {
    public function processDonation(Donation $donation): void;
}

// MonetaryDonation class for monetary donations, implementing IDonateStrategy
class MonetaryDonation implements IDonateStrategy {
    // private float $amount;
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
        $paymentContext = new PaymentContext($this->paymentStrategy);
        $paymentContext->doPayment($donation->getDonationAmount());
    }
}

// NonMonetaryDonation class for non-monetary donations, implementing IDonateStrategy
class NonMonetaryDonation implements IDonateStrategy {
    private string $donatedItem;

    public function processDonation(Donation $donation): void {
        // echo "Processing a non-monetary donation: " . $this->donatedItem . ".\n";
        // Additional logic for processing a non-monetary donation
    }
}

// DonationContext manages the selected donation strategy and donation details
// class DonationContext {
//     private DateTime $date;
//     private float $amount;
//     private string $donatedItem;
//     private IDonateStrategy $donationStrategy;

//     public function __construct(IDonateStrategy $strategy, float $amount = 0.0, string $donatedItem = "") {
//         $this->donationStrategy = $strategy;
//         $this->date = new DateTime(); // current date by default
//         $this->amount = $amount;
//         $this->donatedItem = $donatedItem;
//     }

//     // Set a new donation strategy
//     public function setDonateStrategy(IDonateStrategy $strategy): void {
//         $this->donationStrategy = $strategy;
//     }

//     // Execute the donation strategy's process
//     public function doDonation(): void {
//         $this->donationStrategy->processDonation();
//     }
// }

