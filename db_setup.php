<?php
// Load configurations
require_once "./models/database/IDatabase.php";
$configs = require "server-configs.php";
$conn = Database::getInstance();

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
            // $this->populateData(); // Ensure data is populated if missing
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
            CREATE TABLE IF NOT EXISTS `location` ( 
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
                name VARCHAR(100) NOT NULL
                )");

        $this->conn->query(" 
            CREATE TABLE IF NOT EXISTS `location_hierarchy` ( 
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                parent_id INT NOT NULL, 
                child_id INT NOT NULL,  
                FOREIGN KEY (parent_id) REFERENCES `location`(id), 
                FOREIGN KEY (child_id) REFERENCES `location`(id) )");


        $this->conn->query("
            CREATE TABLE IF NOT EXISTS `event` (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                location_id INT NOT NULL,
                type VARCHAR(50),
                date DATETIME,
                FOREIGN KEY (location_id) REFERENCES `location`(id)
            )");

        $this->conn->query("
            CREATE TABLE IF NOT EXISTS `shop_items` (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                price FLOAT NOT NULL
            )");

        $this->conn->query("
            CREATE TABLE IF NOT EXISTS `shop_categories` (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT
            );");

        $this->conn->query("    
            CREATE TABLE IF NOT EXISTS `category_items` (
                category_id INT NOT NULL,
                item_id INT,
                subcategory_id INT,
                FOREIGN KEY (category_id) REFERENCES shop_categories(id),
                FOREIGN KEY (item_id) REFERENCES shop_items(id),
                FOREIGN KEY (subcategory_id) REFERENCES shop_categories(id)
            );");

        $this->conn->query("
            CREATE TABLE IF NOT EXISTS `cart` (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                status ENUM('current', 'completed') NOT NULL DEFAULT 'current',
                FOREIGN KEY (user_id) REFERENCES `user`(id)
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
            donatorId INT NOT NULL, 
            donationtype ENUM('monetary', 'nonMonetary') NOT NULL,
            donationAmount DOUBLE,
            donatedItem VARCHAR(100),
            paymentType ENUM('paypal', 'creditCard'),
            donationTimestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Payment Table
        $this->conn->query("               
        CREATE TABLE IF NOT EXISTS `payments` (
            paymentId INT AUTO_INCREMENT PRIMARY KEY,
            userId INT NOT NULL, 
            amount DOUBLE,
            description VARCHAR(100),
            paymentType ENUM('paypal', 'creditCard'),
            paymentTimestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Paypal Strategy Table
        $this->conn->query("               
        CREATE TABLE IF NOT EXISTS `paypal` (
            paypalId INT AUTO_INCREMENT PRIMARY KEY,
            paymentId INT NOT NULL,
            paypalEmail VARCHAR(100),
            paypalPassword VARCHAR(100),
            FOREIGN KEY (paymentId) REFERENCES `npo`.`payments`(paymentId) on DELETE CASCADE 
        )");

        // Credit Card Strategy Table
        $this->conn->query("               
        CREATE TABLE IF NOT EXISTS `credit` (
            creditId INT AUTO_INCREMENT PRIMARY KEY,
            paymentId INT NOT NULL,
            cardNumber VARCHAR(100),
            cvv VARCHAR(100),
            expiryDate VARCHAR(100),
            FOREIGN KEY (paymentId) REFERENCES `npo`.`payments`(paymentId) on DELETE CASCADE
        )");

        
        $this->conn->query("
            CREATE TABLE IF NOT EXISTS `cart_items` (
                id INT AUTO_INCREMENT PRIMARY KEY,
                cart_id INT NOT NULL,
                item_id INT NOT NULL,
                quantity INT NOT NULL DEFAULT 1,
                FOREIGN KEY (cart_id) REFERENCES `NPO`.`cart`(id) on DELETE CASCADE,
                FOREIGN KEY (item_id) REFERENCES `NPO`.`shop_items`(id) ON DELETE CASCADE
            )");
    }

    // Method to populate data
    private function populateData()
    {
        // Check if tables have data
        $userCheck = $this->conn->query("SELECT 1 FROM `user` LIMIT 1");
        $shopItemsCheck = $this->conn->query("SELECT 1 FROM `shop_items` LIMIT 1");
        $cartCheck = $this->conn->query("SELECT 1 FROM `cart` LIMIT 1");
        $volunteerEventCheck = $this->conn->query("SELECT 1 FROM `volunteer_events` LIMIT 1");
        $locationCheck = $this->conn->query("SELECT 1 FROM `location` LIMIT 1");
        // Insert data into `user` table
        if ($userCheck->num_rows === 0) {
            $this->conn->query("
                INSERT INTO `user` (type, firstName, lastName, email, passwordHash) VALUES
                    (0, 'Admin', 'Admin', 'admin@admin.com', '25d55ad283aa400af464c76d713c07ad'),
                    (1, 'GitHub', 'User', 'github.user@github.com', '25d55ad283aa400af464c76d713c07ad'),
                    (1, 'Google', 'User', 'google.user@google.com', '25d55ad283aa400af464c76d713c07ad'),
                    (1, 'Facebook', 'User', 'facebook.user@meta.com', '25d55ad283aa400af464c76d713c07ad'),
                    (1, '7amada', 'Belganzabeel', '7amada@belganzabeel.com', '25d55ad283aa400af464c76d713c07ad'),
                    (1, '7amada2', 'Tany', '5ales@depression.inc', '25d55ad283aa400af464c76d713c07ad'),
                    (1, 'r', 'r', 'r', '4b43b0aee35624cd95b910189b3dc231')
            ");
        }



        if ($locationCheck->num_rows === 0) {
            $governorates = ['Cairo', 'Alexandria', 'Giza', 'Port Said', 'Suez', 'Damietta', 'Mansoura', 'Gharbia', 'Ismailia', 'Minya', 'Luxor', 'Aswan', 'Asyut', 'Beni Suef', 'Fayoum', 'Kafr El Sheikh', 'Sharkia', 'Monufia', 'Beheira', 'Matrouh'];
            $subLocations = [
                'Cairo' => ['Nasr City', 'Maadi', 'Heliopolis', 'Dokki', 'Mohandessin', 'Zamalek', 'Ain Shams', 'Shubra', 'Helwan', 'Abbasiya', 'Sayeda Zeinab', 'Garden City', 'Manial', 'Bulaq', 'Imbaba', 'Manshiyat Naser', 'Dar El Salam', 'El Marg', 'El Matareya', 'El Salam City', 'El Sayeda Aisha', 'El Shorouk', 'El Tagamu El Khames', 'El Waily', 'El Zaytoun', 'El Zawya El Hamra', 'El-Nozha', 'Hadayek El Kobba', 'Helmeyet El-Zaitoun', 'Kasr El Nile', 'Korba', 'Madinet Nasr', 'Masr El Qadima', 'New Cairo', 'Old Cairo', 'Rod El-Farag'],
                'Alexandria' => ['Bacos', 'Bolkly', 'Camp Caesar', 'Camp Shezar', 'Cleopatra', 'Dekhela', 'Downtown', 'El Agami', 'El Amreya', 'El Asafra'],
                'Giza' => ['6th of October City', 'Agouza', 'Daher', 'Dokki', 'Faisal', 'Giza Square', 'Haram', 'Imbaba', 'Mohandessin', 'Nahia', 'Nasr City', 'Oseem', 'Pyramids', 'Zamalek'],
                'Port Said' => ['Port Fouad', 'Port Said'],
                'Suez' => ['Ataqah', 'Faisal', 'Ganayen', 'Ganoub', 'Kuwait', 'Suez'],
                'Damietta' => ['New Damietta', 'Ras El Bar', 'Zarqa'],
                'Mansoura' => ['Talkha', 'Dekernes', 'Aga', 'Sherbin'],
                'Gharbia' => ['Tanta', 'Mahalla', 'Zefta', 'Kafr El Zayat'],
                'Ismailia' => ['Fayed', 'Qantara'],
                'Minya' => ['Maghagha', 'Bani Mazar', 'Samalut'],
                'Luxor' => ['Karnak', 'West Bank'],
                'Aswan' => ['Kom Ombo', 'Kalabsha'],
                'Asyut' => ['Abnub', 'Dairut'],
                'Beni Suef' => ['Nasser'],
                'Fayoum' => ['Tamiya', 'Yusuf El Sediaq'],
                'Kafr El Sheikh' => ['Desouk', 'Fuwwah'],
                'Sharkia' => ['Sharkia'],
                'Monufia' => ['Monufia'],
                'Beheira' => ['Beheira'],
                'Matrouh' => ['Matrouh'],
            ];

            foreach ($governorates as $governorate) {
                $this->conn->query("
                INSERT INTO `location` (name) VALUES 
                    ('$governorate') ");

                $parentId = $this->conn->insert_id;
                if (array_key_exists($governorate, $subLocations)) {
                    foreach ($subLocations[$governorate] as $subLocation) { // Insert sub-location 
                        $this->conn->query(" 
                            INSERT INTO `location` (name) VALUES 
                            ('$subLocation')");
                        $childId = $this->conn->insert_id;
                        // Insert relationship into `location_hierarchy` 
                        $this->conn->query(" 
                            INSERT INTO `location_hierarchy` (parent_id, child_id) VALUES 
                                ($parentId, $childId)");
                    }
                }
            }
        }

        $eventCheck = $this->conn->query("SELECT 1 FROM `event` LIMIT 1");
        // Insert data into `event` table
        if ($eventCheck->num_rows === 0) {
            $this->conn->query("
                INSERT INTO `event` (name, description, location_id, type, date) VALUES
                    ('7ayah kareema', 'An 7ayah kareema event', 1, 'Fundraising', '2024-12-25 10:00:00'),
                    ('57357', 'Cancer awareness event', 2, 'Awareness', '2024-12-26 11:00:00'),
                    ('Blood Donation', 'A blood donation event', 3, 'Donation', '2024-12-27 12:00:00')
            ");
        }

        // Insert data into `shop_items` table
        if ($shopItemsCheck->num_rows === 0) {
            $this->conn->query("
                INSERT INTO `shop_items` (name, description, price) VALUES
                    ('Classic Cotton Tee', 'A timeless cotton t-shirt for everyday wear', 20),
                    ('Vintage Graphic Tee', 'Retro graphic print, soft touch', 25.50),
                    ('Sporty Performance Tee', 'Moisture-wicking for active use', 30.65),
                    ('Casual Striped Tee', 'Comfortable striped t-shirt, casual fit', 22.00)
            ");
        }

        // Populate `shop_categories` table
        $categoriesCheck = $this->conn->query("SELECT 1 FROM `shop_categories` LIMIT 1");
        if ($categoriesCheck->num_rows === 0) {
            $this->conn->query("
                INSERT INTO `shop_categories` (name, description) VALUES
                    ('Basic Tees', 'Everyday basic t-shirts for all occasions'),
                    ('Graphic Tees', 'T-shirts with stylish graphic prints'),
                    ('Performance Wear', 'Activewear for sports and fitness'),
                    ('Casual Wear', 'Comfortable casual t-shirts');
          ");
        }
        // Populate `category_items` table to associate items with categories
        $categoryItemsCheck = $this->conn->query("SELECT 1 FROM `category_items` LIMIT 1");
        if ($categoryItemsCheck->num_rows === 0) {
            $this->conn->query("
                 INSERT INTO `category_items` (category_id, item_id) VALUES
                    (1, 1), -- Classic Cotton Tee in Basic Tees
                    (2, 2), -- Vintage Graphic Tee in Graphic Tees
                    (3, 3), -- Sporty Performance Tee in Performance Wear
                     (4, 4); -- Casual Striped Tee in Casual Wear
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
                    (5, 1)
            ");
        }
    }


    function run_query($query, $params = [], $log_errors = true): bool
    {
        $conn = $this->getConnection();

        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            if ($log_errors) error_log("Error preparing statement: " . $conn->error);
            return false;
        }

        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }

        if ($stmt->execute()) {
            return true;
        } else {
            if ($log_errors) error_log("Error: " . $stmt->error);
            return false;
        }
    }

    function run_select_query($query, $params = [], $log_errors = true): mysqli_result|bool
    {
        $conn =$this->getConnection();

        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            if ($log_errors) error_log("Error preparing statement: " . $conn->error);
            return false;
        }

        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($log_errors) {
            error_log("Error: " . $stmt->error);
        }
        return $result;
    }
}

// Initialize database, create tables, and populate data if missing
Database::getInstance();
