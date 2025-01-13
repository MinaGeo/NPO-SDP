<?php
declare(strict_types=1);
ob_start();
require_once "./db_setup.php";
ob_end_clean();

class Donation {
    public static function create(array $data): Donation {
        return new self($data);
    }
    public static function saveDonation(string $donatorName, string $donationType, float $donationAmount, ?string $donatedItem, string $paymentType): bool
    {
        global $configs;

        $query = "INSERT INTO {$configs->DB_NAME}.{$configs->DB_DONATIONS_TABLE} 
                  (donator_name, donation_type, donation_amount, donated_item, payment_type) 
                  VALUES (?, ?, ?, ?, ?)";

        $params = [$donatorName, $donationType, $donationAmount, $donatedItem, $paymentType];
        return run_query($query, $params, true);
    }

    // Retrieve a donation by ID
    public static function getDonationById(int $id): ?Donation
    {
        global $configs;

        $query = "SELECT * FROM {$configs->DB_NAME}.{$configs->DB_DONATIONS_TABLE} WHERE id = ?";
        $rows = run_select_query($query, [$id]);

        return $rows && $rows->num_rows > 0 ? new Donation($rows->fetch_assoc()) : null;
    }

    // Retrieve all donations
    public static function getAllDonations(): array
    {
        global $configs;

        $query = "SELECT * FROM {$configs->DB_NAME}.{$configs->DB_DONATIONS_TABLE}";
        $rows = run_select_query($query)->fetch_all(MYSQLI_ASSOC);

        $donations = [];
        $donationIterator = new itemIterator($rows);
        while ($donationIterator->hasNext()) {
            $donations[] = Donation::create($donationIterator->next());
        }

        return $donations;
    }

    // Update a donation
    public static function updateDonation(int $id, string $donatorName, string $donationType, float $donationAmount, ?string $donatedItem, string $paymentType): bool
    {
        global $configs;

        $query = "UPDATE {$configs->DB_NAME}.{$configs->DB_DONATIONS_TABLE} 
                  SET donator_name = ?, donation_type = ?, donation_amount = ?, donated_item = ?, payment_type = ? 
                  WHERE id = ?";

        $params = [$donatorName, $donationType, $donationAmount, $donatedItem, $paymentType, $id];
        return run_query($query, $params, true);
    }

    // Delete a donation
    public static function deleteDonation(int $id): bool
    {
        global $configs;

        $query = "DELETE FROM {$configs->DB_NAME}.{$configs->DB_DONATIONS_TABLE} WHERE id = ?";
        return run_query($query, [$id], true);
    }
    
}


