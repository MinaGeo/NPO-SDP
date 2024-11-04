<?php
include_once 'config.php';

class Donation {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function saveDonation($donatorName, $donationType, $donationAmount, $donatedItem, $paymentType) {
        $stmt = $this->pdo->prepare("INSERT INTO donations (donator_name, donation_type, donation_amount, donated_item, payment_type) VALUES (:donatorName, :donationType, :donationAmount, :donatedItem, :paymentType)");

        $stmt->execute([
            ':donatorName' => $donatorName,
            ':donationType' => $donationType,
            ':donationAmount' => $donationAmount,
            ':donatedItem' => $donatedItem,
            ':paymentType' => $paymentType
        ]);
    }
}

