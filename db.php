<?php
$servername = "localhost";
$username = "root";
$db_name = "NPO";

// Create initial connection
$conn = new mysqli($servername, $username);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully<br/><hr/>";

// Check if database exists
$result = $conn->query("SHOW DATABASES LIKE '$db_name'");
if ($result->num_rows == 0) {
    // Database doesn't exist, create it
    if ($conn->query("CREATE DATABASE `$db_name`") === TRUE) {
        echo "Database created successfully<br/><hr/>";

        // Close the initial connection
        $conn->close();

        // Reconnect to the new database
        $conn = new mysqli($servername, $username, "", $db_name);

        if ($conn->connect_error) {
            die("Reconnection failed: " . $conn->connect_error);
        }

        echo "Reconnected successfully to $db_name database<br/><hr/>";

        // Create tables
        $createEventTableQuery = "
            CREATE TABLE IF NOT EXISTS `event` (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                location VARCHAR(50),
                type VARCHAR(50),
                date DATETIME
            )";
        $createShopItemsTableQuery = "
            CREATE TABLE IF NOT EXISTS `shop_items` (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                price INT NOT NULL
            )";

        if ($conn->query($createEventTableQuery) === TRUE && $conn->query($createShopItemsTableQuery) === TRUE) {
            echo "Tables created successfully<br/><hr/>";
        } else {
            echo "Error creating tables: " . $conn->error;
        }

        // Insert data into tables
        $insertEventQuery = "
            INSERT INTO `event` (name, description, location, type, date)
            VALUES
                ('7ayah kareema', 'An 7ayah kareema event', 'Cairo', 'Fundraising', '2024-12-25 10:00:00'),
                ('57357', 'Cancer awareness event', 'Cairo', 'Awareness', '2024-12-26 11:00:00'),
                ('Blood Donation', 'A blood donation event', 'Cairo', 'Donation', '2024-12-27 12:00:00')";
        $insertShopItemsQuery = "
            INSERT INTO `shop_items` (name, description, price)
            VALUES
                ('Classic Cotton Tee', 'A timeless cotton t-shirt for everyday wear', 20),
                ('Vintage Graphic Tee', 'Retro graphic print, soft touch', 25),
                ('Sporty Performance Tee', 'Moisture-wicking for active use', 30),
                ('Casual Striped Tee', 'Comfortable striped t-shirt, casual fit', 22)";

        if ($conn->query($insertEventQuery) === TRUE && $conn->query($insertShopItemsQuery) === TRUE) {
            echo "Data inserted successfully<br/><hr/>";
        } else {
            echo "Error inserting data: " . $conn->error;
        }
    } else {
        echo "Error creating database: " . $conn->error;
    }
} else {
    echo "Database already exists, just making the connection<br/><hr/>";

    // Close the initial connection
    $conn->close();

    // Reconnect to the existing database
    $conn = new mysqli($servername, $username, "", $db_name);

    if ($conn->connect_error) {
        die("Reconnection failed: " . $conn->connect_error);
    }

    echo "Reconnected successfully to $db_name database<br/><hr/>";
}

function run_query($query, $params = [], $echo = false): bool
{
    global $conn;

    // Prepare statement
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        if ($echo) echo "Error preparing statement: " . $conn->error;
        return false;
    }

    // Bind parameters if any
    if (!empty($params)) {
        $types = str_repeat('s', count($params)); // Assuming all parameters are strings
        $stmt->bind_param($types, ...$params);
    }

    // Execute the statement
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
    global $conn;

    // Prepare statement
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        if ($echo) echo "Error preparing statement: " . $conn->error;
        return false;
    }

    // Bind parameters if any
    if (!empty($params)) {
        $types = str_repeat('s', count($params)); // Assuming all parameters are strings
        $stmt->bind_param($types, ...$params);
    }

    // Execute the statement
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
