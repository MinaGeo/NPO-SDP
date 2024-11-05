<?php
$configs = require "server-configs.php";

// echo "Connecting to $configs->DB_HOST, $configs->DB_USER, $configs->DB_PASS, $configs->DB_NAME </br>";

// Create initial connection
$conn = new mysqli($configs->DB_HOST, $configs->DB_USER, $configs->DB_PASS);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully<br/><hr/>";

// Check if the database exists
$db_name = $configs->DB_NAME;
$result = $conn->query("SHOW DATABASES LIKE '$db_name'");

if ($result->num_rows == 0) {
    // Database doesn't exist, create it
    if ($conn->query("CREATE DATABASE `$db_name`") === TRUE) {
        // echo "Database created successfully<br/><hr/>";

        // Close the initial connection and reconnect to the new database
        $conn->close();
        $conn = new mysqli($configs->DB_HOST, $configs->DB_USER, $configs->DB_PASS, $db_name);

        if ($conn->connect_error) {
            die("Reconnection failed: " . $conn->connect_error);
        }
        // echo "Reconnected successfully to $db_name database<br/><hr/>";

        //////////////////////Create Tables//////////////////////
        // User Table
        $createUserTableQuery = "
            CREATE TABLE IF NOT EXISTS `user` (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                type INT DEFAULT 1,
                firstName VARCHAR(50) NULL DEFAULT NULL,
                lastName VARCHAR(50) NULL DEFAULT NULL,
                email VARCHAR(50) NULL,
                passwordHash VARCHAR(32) NOT NULL,
                UNIQUE INDEX `uq_email` (`email` ASC)
            )";
        // Event Table
        $createEventTableQuery = "
            CREATE TABLE IF NOT EXISTS `event` (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                location VARCHAR(50),
                type VARCHAR(50),
                date DATETIME
            )";
        // Shop Items Table
        $createShopItemsTableQuery = "
            CREATE TABLE IF NOT EXISTS `shop_items` (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                price INT NOT NULL
            )";
        // Cart Table
        $createCartTableQuery = "
            CREATE TABLE IF NOT EXISTS `cart` (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                FOREIGN KEY (user_id) REFERENCES `NPO`.`user`(id)
            )";
        // Cart Items Table
        $createCartItemsTableQuery = "
            CREATE TABLE IF NOT EXISTS `cart_items` (
                id INT AUTO_INCREMENT PRIMARY KEY,
                cart_id INT NOT NULL,
                item_id INT NOT NULL,
                quantity INT NOT NULL DEFAULT 1,
                FOREIGN KEY (cart_id) REFERENCES `NPO`.`cart`(id) on DELETE CASCADE,
                FOREIGN KEY (item_id) REFERENCES `NPO`.`shop_items`(id)
            )";
        // 
        $createVolunteerEventsTableQuery = "
            CREATE TABLE IF NOT EXISTS volunteer_events (
                id INT AUTO_INCREMENT PRIMARY KEY,
                volunteer_id INT NOT NULL,
                event_id INT NOT NULL,
                FOREIGN KEY (volunteer_id) REFERENCES user(id) ON DELETE CASCADE,
                FOREIGN KEY (event_id) REFERENCES event(id) ON DELETE CASCADE
            )";

        ////////////////////////////////////////////////////////////////////////////

        if (
            $conn->query($createUserTableQuery) === TRUE &&
            $conn->query($createEventTableQuery) === TRUE &&
            $conn->query($createShopItemsTableQuery) === TRUE &&
            $conn->query($createCartTableQuery) === TRUE &&
            $conn->query($createCartItemsTableQuery) === TRUE &&
            $conn->query($createVolunteerEventsTableQuery) === TRUE
        ) {
            // echo "Tables created successfully<br/><hr/>";
        } else {
            echo "Error creating tables: " . $conn->error;
        }

        //////////////////////Insert Data//////////////////////
        // Insert User Data
        $insertAdminQuery = "
            INSERT INTO `user` (type, firstName, lastName, email, passwordHash)
            VALUES
                (0,'Admin','Admin','admin@admin.com','25d55ad283aa400af464c76d713c07ad')";
        $insertUserQuery = "
            INSERT INTO `user` (type, firstName, lastName, email, passwordHash)
            VALUES
                (1,'GitHub','User','github.user@github.com','25d55ad283aa400af464c76d713c07ad'),
                (1,'Google','User','google.user@google.com','25d55ad283aa400af464c76d713c07ad'),
                (1,'Facebook','User','facebook.user@meta.com','25d55ad283aa400af464c76d713c07ad'),
                (1,'7amada','Belganzabeel','7amada@belganzabeel.com','25d55ad283aa400af464c76d713c07ad'),
                (1,'7amada','Tany','5ales@depression.inc','25d55ad283aa400af464c76d713c07ad'),
                (1, 'r', 'r', 'r', '4b43b0aee35624cd95b910189b3dc231')";
        // Insert Event Data
        $insertEventQuery = "
            INSERT INTO `event` (name, description, location, type, date)
            VALUES
                ('7ayah kareema', 'An 7ayah kareema event', 'Cairo', 'Fundraising', '2024-12-25 10:00:00'),
                ('57357', 'Cancer awareness event', 'Cairo', 'Awareness', '2024-12-26 11:00:00'),
                ('Blood Donation', 'A blood donation event', 'Cairo', 'Donation', '2024-12-27 12:00:00')";
        // Insert Shop Items Data
        $insertShopItemsQuery = "
            INSERT INTO `shop_items` (name, description, price)
            VALUES
                ('Classic Cotton Tee', 'A timeless cotton t-shirt for everyday wear', 20),
                ('Vintage Graphic Tee', 'Retro graphic print, soft touch', 25),
                ('Sporty Performance Tee', 'Moisture-wicking for active use', 30),
                ('Casual Striped Tee', 'Comfortable striped t-shirt, casual fit', 22)";
        // Insert Cart Data
        $insertCartQuery = "
            INSERT INTO `cart` (user_id)
            VALUES
                (1),
                (2),
                (3),
                (4),
                (5)";
        $insertVolunteerEvent = "
        INSERT INTO `volunteer_events` (volunteer_id, event_id) VALUES
(7, 1),
(6, 1),
(7, 2),
(6, 2),
(5, 2),
(5, 1)";
        ////////////////////////////////////////////////////////////////////////////

        if (
            $conn->query($insertAdminQuery) === TRUE &&
            $conn->query($insertUserQuery) === TRUE &&
            $conn->query($insertEventQuery) === TRUE &&
            $conn->query($insertShopItemsQuery) === TRUE &&
            $conn->query($insertCartQuery) === TRUE &&
            $conn->query($insertVolunteerEvent) === TRUE
        ) {
            // echo "Data inserted successfully<br/><hr/>";
        } else {
            echo "Error inserting data: " . $conn->error;
        }
    } else {
        echo "Error creating database: " . $conn->error;
    }
} else {
    // echo "Database already exists, just making the connection<br/><hr/>";

    // Close the initial connection and reconnect to the existing database
    $conn->close();
    $conn = new mysqli($configs->DB_HOST, $configs->DB_USER, $configs->DB_PASS, $db_name);

    if ($conn->connect_error) {
        die("Reconnection failed: " . $conn->connect_error);
    }
    // echo "Reconnected successfully to $db_name database<br/><hr/>";
}

// Function to run non-select queries
function run_query($query, $params = [], $echo = false): bool
{
    global $conn;

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

// Function to run select queries and fetch results
function run_select_query($query, $params = [], $echo = false): mysqli_result|bool
{
    global $conn;

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

// Close the connection when done
// $conn->close();
