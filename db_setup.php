<?php
$configs = require "server-configs.php";
echo "Connecting to $configs->DB_HOST, $configs->DB_USER, $configs->DB_PASS, $configs->DB_NAME </br>";
$conn = new mysqli($configs->DB_HOST, $configs->DB_USER, $configs->DB_PASS, $configs->DB_NAME);
// $conn = new mysqli($configs->DB_HOST, $configs->DB_USER);
// $mysqli = new mysqli("DB_HOST", "DB_USER", "password", "database_name");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully<br/><hr/>";

function run_queries($queries, $echo = false): array
{
    global $conn;
    $ret = [];
    foreach ($queries as $query) {
        $ret += [$conn->query($query)];
        if ($echo) {
            echo '<pre>' . $query . '</pre>';
            echo $ret[array_key_last($ret)] === TRUE ? "Query ran successfully<br/>" : "Error: " . $conn->error;
            echo "<hr/>";
        }
    }
    return $ret;
}

function run_query($query, $echo = false): bool
{
    return run_queries([$query], $echo)[0];
}

function run_select_query($query, $echo = false): mysqli_result|bool
{
    global $conn;
    $result = $conn->query($query);
    if ($echo) {
        echo '<pre>' . $query . '</pre>';
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc())
                echo $row;
        } else {
            echo "0 results";
        }
        echo "<hr/>";
    }
    return $result;
}

// $conn->close();
