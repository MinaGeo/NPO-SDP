<?php
$configs = require "server-configs.php";

echo "Connecting to $configs->DB_HOST, $configs->DB_USER, $configs->DB_PASS, $configs->DB_NAME </br>";

// Create initial connection
$conn = new mysqli($configs->DB_HOST, $configs->DB_USER, $configs->DB_PASS);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully<br/><hr/>";

// Check if the database exists
$db_name = $configs->DB_NAME;
$result = $conn->query("SHOW DATABASES LIKE '$db_name'");

if ($result->num_rows == 0) {
    // Database doesn't exist, create it
    if ($conn->query("CREATE DATABASE `$db_name`") === TRUE) {
        echo "Database created successfully<br/><hr/>";
        
        // Close the initial connection and reconnect to the new database
        $conn->close();
        $conn = new mysqli($configs->DB_HOST, $configs->DB_USER, $configs->DB_PASS, $db_name);

        if ($conn->connect_error) {
            die("Reconnection failed: " . $conn->connect_error);
        }
        echo "Reconnected successfully to $db_name database<br/><hr/>";

        // Create `event` table
        $createTableQuery = "
            CREATE TABLE IF NOT EXISTS `event` (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                location VARCHAR(50),
                type VARCHAR(50),
                date DATETIME
            );
        ";

        if ($conn->query($createTableQuery) === TRUE) {
            echo "Table created successfully<br/><hr/>";
        } else {
            echo "Error creating table: " . $conn->error;
        }

        // Insert initial data into `event` table
        $insertQuery = "
            INSERT INTO `event` (name, description, location, type, date)
            VALUES
                ('7ayah kareema', 'An 7ayah kareema event', 'Cairo', 'Fundraising', '2024-12-25 10:00:00'),
                ('57357', 'Cancer awareness event', 'Cairo', 'Awareness', '2024-12-26 11:00:00'),
                ('Blood Donation', 'A blood donation event', 'Cairo', 'Donation', '2024-12-27 12:00:00');
        ";

        if ($conn->query($insertQuery) === TRUE) {
            echo "Data inserted successfully<br/><hr/>";
        } else {
            echo "Error inserting data: " . $conn->error;
        }
    } else {
        echo "Error creating database: " . $conn->error;
    }
} else {
    echo "Database already exists, just making the connection<br/><hr/>";

    // Close the initial connection and reconnect to the existing database
    $conn->close();
    $conn = new mysqli($configs->DB_HOST, $configs->DB_USER, $configs->DB_PASS, $db_name);

    if ($conn->connect_error) {
        die("Reconnection failed: " . $conn->connect_error);
    }
    echo "Reconnected successfully to $db_name database<br/><hr/>";
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
?>
