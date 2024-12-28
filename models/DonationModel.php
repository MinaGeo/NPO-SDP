<?php
declare(strict_types=1);
ob_start();
require_once "./db_setup.php";
require_once "./models/Donation_State_Interfaces.php"; 
ob_end_clean();

class Donation{
    /* ------------------- Attributes -------------------  */
    // Attributes
    private int $id;
    private string $itemName;
    private int $donatorId;
    private int $donationType;
    private float $donationAmount;
    private int $paymentType;
    private string $donationTimestamp;

    // State attribute
    private IDonationState $state;

    /* ------------------- State Management Functions -------------------  */
    public function setState(IDonationState $state):void{
        $this->state = $state;
    }

    public function executeState():void{
        $this->state->execute();
    }

    public function nextState():void{
        $this->state->next($this);
    }

    public function previousState():void{
        $this->state->previous($this);
    }

    /* ------------------- Database Interfacing Functions -------------------  */
    // Database fetch function 
    public static function get_by_id(int $id): ?Donation{
        global $configs;
        $rows = run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_DONATIONS_TABLE WHERE id = '$id'");
        return $rows->num_rows > 0 ? new User($rows->fetch_assoc()) : null;
    }

    // Database fetch function to fetch all donations in the database 
    public static function get_all(){
        global $configs;
        $rows = run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_DONATIONS_TABLE");
        $donations = [];
        while($row = $rows->fetch_assoc()){
            $donations[] = new Donation($row);
        }
        return $donations;
    }

    // Create a new record in the database
    static public function create_new_donation(array $donationData): bool
    {
        global $configs;
        $columns = implode(", ", array_keys($donationData));
        $values = implode("', '", array_values($donationData));
        $query = "INSERT INTO $configs->DB_NAME.$configs->DB_USERS_TABLE ($columns) VALUES ('$values')";
        return run_query($query);
    }

    // Remove a record from the database given an ID
    static public function remove_by_id(int $id): bool
    {
        global $configs;
        $query = "DELETE FROM $configs->DB_NAME.$configs->DB_DONATIONS_TABLE WHERE id = '$id'";
        return run_query($query);
    }
}


