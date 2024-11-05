<?php
// Define IPay interface for payment strategies
interface IPay {
    public function pay(float $paymentAmount): bool;
    public function collectPaymentDetails(): void;
    public function verifyPaymentCredentials(): bool;
}

// PayByPaypal class, implementing IPay for PayPal payments
class PayByPaypal implements IPay {
    private string $email;
    private string $password;

    public function __construct(string $email, string $password) {
        $this->email = $email;
        $this->password = $password;
    }

    public function collectPaymentDetails(): void {
        echo "Collecting PayPal payment details.\n";
    }

    public function verifyPaymentCredentials(): bool {
        // Verify credentials (simulated)
        return true;
    }

    public function pay(float $paymentAmount): bool {
        if ($this->verifyPaymentCredentials()) {
            echo "Processed PayPal payment of $" . $paymentAmount . ".\n";
            return true;
        }
        return false;
    }
}

// PayByCreditCard class, implementing IPay for credit card payments
class PayByCreditCard implements IPay {
    private string $number;
    private string $cvv;
    private string $expiryDate;

    public function __construct(string $number, string $cvv, string $expiryDate) {
        $this->number = $number;
        $this->cvv = $cvv;
        $this->expiryDate = $expiryDate;
    }

    public function collectPaymentDetails(): void {
        echo "Collecting credit card payment details.\n";
    }

    public function verifyPaymentCredentials(): bool {
        // Verify card credentials (simulated)
        return true;
    }

    public function pay(float $paymentAmount): bool {
        if ($this->verifyPaymentCredentials()) {
            echo "Processed credit card payment of $" . $paymentAmount . ".\n";
            return true;
        }
        return false;
    }
}

// PaymentContext to manage and execute payment strategies
class PaymentContext {
    private IPay $strategy;

    public function __construct(IPay $strategy) {
        $this->strategy = $strategy;
    }

    public function setStrategy(IPay $strategy): void {
        $this->strategy = $strategy;
    }

    public function doPayment(float $amount): bool {
        return $this->strategy->pay($amount);
    }
}

