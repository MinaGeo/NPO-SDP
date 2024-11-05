<?php
// Load configurations
$configs = require "server-configs.php";

class Database
{
    private static $instance = null;
    private $conn;

    private function __construct($configs)
    {
        $this->conn = new mysqli($configs->DB_HOST, $configs->DB_USER, $configs->DB_PASS);

        if ($this->conn->connect_error) {
            die("Initial connection failed: " . $this->conn->connect_error);
        }

        // Check if database exists and create if not
        $db_name = $configs->DB_NAME;
        $result = $this->conn->query("SHOW DATABASES LIKE '$db_name'");

        if ($result->num_rows == 0) {
            // Database doesn't exist, so create it
            if ($this->conn->query("CREATE DATABASE `$db_name`") === TRUE) {
                $this->conn->select_db($db_name);
                $this->createTables();
                $this->populateData(); // Populate tables after creation
            } else {
                die("Error creating database: " . $this->conn->error);
            }
        } else {
            // Database exists, reconnect to it
            $this->conn->select_db($db_name);
            $this->populateData(); // Ensure data is populated if missing
        }
    }

    public static function getInstance()
    {
        global $configs;
        if (!self::$instance) {
            self::$instance = new Database($configs);
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }

    private function createTables()
    {
        $this->conn->query("
            CREATE TABLE IF NOT EXISTS `user` (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                type INT DEFAULT 1,
                firstName VARCHAR(50) NULL DEFAULT NULL,
                lastName VARCHAR(50) NULL DEFAULT NULL,
                email VARCHAR(50) NULL,
                passwordHash VARCHAR(32) NOT NULL,
                UNIQUE INDEX `uq_email` (`email` ASC)
            )");

        $this->conn->query("
            CREATE TABLE IF NOT EXISTS `event` (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                location VARCHAR(50),
                type VARCHAR(50),
                date DATETIME
            )");

        $this->conn->query("
            CREATE TABLE IF NOT EXISTS `shop_items` (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                price INT NOT NULL
            )");

        $this->conn->query("
            CREATE TABLE IF NOT EXISTS `cart` (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                FOREIGN KEY (user_id) REFERENCES `user`(id)
            )");

        $this->conn->query("
            CREATE TABLE IF NOT EXISTS `cart_items` (
                id INT AUTO_INCREMENT PRIMARY KEY,
                cart_id INT NOT NULL,
                item_id INT NOT NULL,
                quantity INT NOT NULL DEFAULT 1,
                FOREIGN KEY (cart_id) REFERENCES `NPO`.`cart`(id) on DELETE CASCADE,
                FOREIGN KEY (item_id) REFERENCES `NPO`.`shop_items`(id)
            )");

        $this->conn->query("               
            CREATE TABLE IF NOT EXISTS `volunteer_events` (
                id INT AUTO_INCREMENT PRIMARY KEY,
                volunteer_id INT NOT NULL,
                event_id INT NOT NULL,
                FOREIGN KEY (volunteer_id) REFERENCES user(id) ON DELETE CASCADE,
                FOREIGN KEY (event_id) REFERENCES event(id) ON DELETE CASCADE
            )");


        $this->conn->query("               
            CREATE TABLE IF NOT EXISTS `donations` (
                id INT AUTO_INCREMENT PRIMARY KEY,
                donator_name VARCHAR(100) NOT NULL,
                donation_type ENUM('monetary', 'nonMonetary') NOT NULL,
                donation_amount DOUBLE,
                donated_item VARCHAR(255),
                payment_type ENUM('paypal', 'creditCard'),
                donation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
    }

    // Method to populate data
    private function populateData()
    {
        // Check if tables have data
        $userCheck = $this->conn->query("SELECT 1 FROM `user` LIMIT 1");
        $eventCheck = $this->conn->query("SELECT 1 FROM `event` LIMIT 1");
        $shopItemsCheck = $this->conn->query("SELECT 1 FROM `shop_items` LIMIT 1");
        $cartCheck = $this->conn->query("SELECT 1 FROM `cart` LIMIT 1");
        $volunteerEventCheck = $this->conn->query("SELECT 1 FROM `volunteer_events` LIMIT 1");

        // Insert data into `user` table
        if ($userCheck->num_rows === 0) {
            $this->conn->query("
                INSERT INTO `user` (type, firstName, lastName, email, passwordHash) VALUES
                    (0, 'Admin', 'Admin', 'admin@admin.com', '25d55ad283aa400af464c76d713c07ad'),
                    (1, 'GitHub', 'User', 'github.user@github.com', '25d55ad283aa400af464c76d713c07ad'),
                    (1, 'Google', 'User', 'google.user@google.com', '25d55ad283aa400af464c76d713c07ad'),
                    (1, 'Facebook', 'User', 'facebook.user@meta.com', '25d55ad283aa400af464c76d713c07ad'),
                    (1, '7amada', 'Belganzabeel', '7amada@belganzabeel.com', '25d55ad283aa400af464c76d713c07ad'),
                    (1, '7amada', 'Tany', '5ales@depression.inc', '25d55ad283aa400af464c76d713c07ad')
            ");
        }

        // Insert data into `event` table
        if ($eventCheck->num_rows === 0) {
            $this->conn->query("
                INSERT INTO `event` (name, description, location, type, date) VALUES
                    ('7ayah kareema', 'An 7ayah kareema event', 'Cairo', 'Fundraising', '2024-12-25 10:00:00'),
                    ('57357', 'Cancer awareness event', 'Cairo', 'Awareness', '2024-12-26 11:00:00'),
                    ('Blood Donation', 'A blood donation event', 'Cairo', 'Donation', '2024-12-27 12:00:00')
            ");
        }

        // Insert data into `shop_items` table
        if ($shopItemsCheck->num_rows === 0) {
            $this->conn->query("
                INSERT INTO `shop_items` (name, description, price) VALUES
                    ('Classic Cotton Tee', 'A timeless cotton t-shirt for everyday wear', 20),
                    ('Vintage Graphic Tee', 'Retro graphic print, soft touch', 25),
                    ('Sporty Performance Tee', 'Moisture-wicking for active use', 30),
                    ('Casual Striped Tee', 'Comfortable striped t-shirt, casual fit', 22)
            ");
        }

        // Insert data into `cart` table
        if ($cartCheck->num_rows === 0) {
            $this->conn->query("
                INSERT INTO `cart` (user_id) VALUES
                    (1),
                    (2),
                    (3),
                    (4),
                    (5)
            ");
        }
        if ($cartCheck->num_rows === 0) {
            $this->conn->query("
        INSERT INTO `volunteer_events` (volunteer_id, event_id) VALUES
(7, 1),
(6, 1),
(7, 2),
(6, 2),
(5, 2),
(5, 1)");
        }
    }
}

// Helper functions
function run_query($query, $params = [], $echo = false): bool
{
    $conn = Database::getInstance()->getConnection();

    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        if ($echo) echo "Error preparing statement: " . $conn->error;
        return false;
    }

    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }

    if ($stmt->execute()) {
        if ($echo) echo "Query ran successfully<br/>";
        return true;
    } else {
        if ($echo) echo "Error: " . $stmt->error;
        return false;
    }
}

function run_select_query($query, $params = [], $echo = false): mysqli_result|bool
{
    $conn = Database::getInstance()->getConnection();

    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        if ($echo) echo "Error preparing statement: " . $conn->error;
        return false;
    }

    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($echo) {
        echo '<pre>' . $query . '</pre>';
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo print_r($row, true);
            }
        } else {
            echo "0 results";
        }
        echo "<hr/>";
    }

    return $result;
}

// Initialize database, create tables, and populate data if missing
Database::getInstance();
