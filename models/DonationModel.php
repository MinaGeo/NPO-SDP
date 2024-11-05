<?php
declare(strict_types=1);
ob_start();
require_once "./db_setup.php";
ob_end_clean();

class Donation {
    public static function saveDonation($donatorName, $donationType, $donationAmount, $donatedItem, $paymentType): bool
    {
        global $configs;
    
        // SQL query with placeholders for prepared statement
        $query = "INSERT INTO {$configs->DB_NAME}.{$configs->DB_DONATIONS_TABLE} 
                  (donator_name, donation_type, donation_amount, donated_item, payment_type) 
                  VALUES (?, ?, ?, ?, ?)";
    
        // Prepare the query parameters
        $params = [$donatorName, $donationType, $donationAmount, $donatedItem, $paymentType];
    
        // Run the query with the parameters
        return run_query($query, $params, true); // 'true' to indicate that we're expecting a result
    }
    
}


