<?php
declare(strict_types=1);
ob_start();
require_once "./db_setup.php";
require_once "./models/Donation_State_Interfaces.php"; 
// require_once "./models/Donation_State_Pending.php";
require_once "./models/Donation_State_GetData.php";
require_once "./models/PaymentClasses.php";
ob_end_clean();

class Donation{
    /* ------------------- Attributes -------------------  */
    // Attributes
    // id INT AUTO_INCREMENT PRIMARY KEY,
    // donatorId INT NOT NULL, 
    // donationtype ENUM('monetary', 'nonMonetary') NOT NULL,
    // donationAmount DOUBLE,
    // donatedItem VARCHAR(100),
    // paymentType ENUM('paypal', 'creditCard'),
    // donationTimestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP

    private $id;
    // private string $itemName;
    private $donatorId;
    private $donationType;
    private $donationAmount;
    private $donatedItem;
    private $paymentType;
    private $donationTimestamp;

    // Donation Type Strategies
    private IDonateStrategy $donationStrategy;

    // State attribute
    private IDonationState $state;

    /* ------------------- Constructor -------------------  */
    // Constructor
    public function __construct()
    {
        $this->state = new DonationGetDataState();
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getDonatorId()
    {
        return $this->donatorId;
    }

    public function getDonationType()
    {
        return $this->donationType;
    }

    public function getDonationAmount()
    {
        return $this->donationAmount;
    }

    public function getPaymentType()
    {
        return $this->paymentType;
    }

    public function getDonationTimestamp()
    {
        return $this->donationTimestamp;
    }

    public function getDonatedItem()
    {
        return $this->donatedItem;
    }

    // Setters
    public function setId($id): void
    {
        $this->id = $id;
    }

    public function setDonatorId($donatorId): void
    {
        $this->donatorId = $donatorId;
    }

    public function setDonationType($donationType): void
    {
        $this->donationType = $donationType;
    }

    public function setDonationAmount($donationAmount): void
    {
        $this->donationAmount = $donationAmount;
    }

    public function setPaymentType($paymentType): void
    {
        $this->paymentType = $paymentType;
    }

    public function setDonationTimestamp($donationTimestamp): void
    {
        $this->donationTimestamp = $donationTimestamp;
    }

    public function setDonatedItem($donatedItem): void
    {
        $this->donatedItem = $donatedItem;
    }

    /* ------------------- State Management Functions -------------------  */
    public function setState(IDonationState $state):void{
        $this->state = $state;
    }

    public function executeState():void{
        $this->state->execute($this);
    }

    public function nextState():void{
        $this->state->next($this);
    }

    public function previousState():void{
        $this->state->previous($this);
    }

    /* ------------------- Donation Type Strategy -------------------  */
    public function setDonationStrategy(IDonateStrategy $strategy):void{
        $this->donationStrategy = $strategy;
    }

    public function processDonation():void{
        $this->donationStrategy->processDonation($this);
        // $this->donationStrategy->processDonation();
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
        $query = "INSERT INTO $configs->DB_NAME.$configs->DB_DONATIONS_TABLE ($columns) VALUES ('$values')";
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


