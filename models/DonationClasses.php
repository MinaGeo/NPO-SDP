<?php
// Define the IDonateStrategy interface for donation strategies
interface IDonateStrategy {
    public function processDonation(): void;
}

// MonetaryDonation class for monetary donations, implementing IDonateStrategy
class MonetaryDonation implements IDonateStrategy {
    private float $amount;

    public function __construct(float $amount) {
        $this->amount = $amount;
    }

    public function processDonation(): void {
        echo "Processing a monetary donation of $" . $this->amount . ".\n";
        // Additional logic for processing a monetary donation
    }
}

// NonMonetaryDonation class for non-monetary donations, implementing IDonateStrategy
class NonMonetaryDonation implements IDonateStrategy {
    private string $donatedItem;

    public function __construct(string $item) {
        $this->donatedItem = $item;
    }

    public function processDonation(): void {
        echo "Processing a non-monetary donation: " . $this->donatedItem . ".\n";
        // Additional logic for processing a non-monetary donation
    }
}

// DonationContext manages the selected donation strategy and donation details
class DonationContext {
    private DateTime $date;
    private float $amount;
    private string $donatedItem;
    private IDonateStrategy $donationStrategy;

    public function __construct(IDonateStrategy $strategy, float $amount = 0.0, string $donatedItem = "") {
        $this->donationStrategy = $strategy;
        $this->date = new DateTime(); // current date by default
        $this->amount = $amount;
        $this->donatedItem = $donatedItem;
    }

    // Set a new donation strategy
    public function setDonateStrategy(IDonateStrategy $strategy): void {
        $this->donationStrategy = $strategy;
    }

    // Execute the donation strategy's process
    public function doDonation(): void {
        $this->donationStrategy->processDonation();
    }
}

