<?php

require_once "./models/database/IDatabase.php";
require_once "./models/userBase.php";
$configs = require "./server-configs.php";
require_once "./db_setup.php";

class DatabaseProxy implements IDatabase
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function validateUser() : bool
{
    // Check if the session user ID is set and is a valid integer
    if (!isset($_SESSION['USER_ID']) || !is_numeric($_SESSION['USER_ID'])) {
        return false; // Invalid or missing user ID
    }

    // Retrieve the user based on the session ID
    $user = User::get_by_id((int)$_SESSION['USER_ID']);

    // Ensure the user exists
    if ($user === null) {
        return false; // User not found
    }

    // Check if the user type is valid (e.g., '1' for admin, '0' for guest )
    if (!in_array($user->getType(), [0, 1, 2])) { 
        return false; // Invalid user type
    }

    // Validate first name and last name (ensure they are non-empty strings)
    if (empty(trim($user->getFirstName())) || empty(trim($user->getLastName()))) {
        return false; // Invalid name data
    }

    // Validate email format
    if (!filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
        return false; // Invalid email
    }

    // Ensure password hash is not empty
    if (empty($user->getPasswordHash())) {
        return false; // Password hash missing
    }

    // All checks passed
    return true;
}


    public function run_query($query, $params = [], $echo = false)
    {
        global $conn;
        if($this->validateUser()){
            return $conn->run_query($query);
        }
        // return $this->database->run_query($query, $params, $echo);
    }

    public function run_select_query($query, $params = [], $echo = false) : mysqli_result|bool
    {
        global $conn;
        if($this->validateUser()){
            return $conn->run_select_query($query);
        }
    }
}

?>