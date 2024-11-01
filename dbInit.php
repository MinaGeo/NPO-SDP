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
function run_query($query, $echo = false): bool
{
    global $conn;
    if ($echo) echo '<pre>' . $query . '</pre>';

    // Execute the query and check for success
    if ($conn->query($query) === TRUE) {
        if ($echo) echo "Query ran successfully<br/>";
        return true; // Return true for successful execution
    } else {
        if ($echo) echo "Error: " . $conn->error;
        return false; // Return false if there's an error
    }
}

// Drop database if exists
run_query("DROP DATABASE IF EXISTS `NPO`");
?>