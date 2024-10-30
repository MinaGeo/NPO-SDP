<?php

declare(strict_types=1);

ob_start();
require_once "../db-connection-setup.php";
ob_end_clean();

class User
{
    private function __construct($properties)
    {
        foreach ($properties as $prop => $value) {
            $this->{$prop} = $value;
        }
    }
    public function __tostring()
    {
        $str = '<pre>';
        foreach ($this as $key => $value) {
            $str .= "$key: $value<br/>";
        }
        return $str . '</pre>';
    }

    // Creates and returns a User object given an ID that exists in the database, otherwise null
    static public function get_by_id($id): User|null
    {
        // This is a static function that is shared between all instances of the class (all user objects)
        // This function does not required instantiation for callin
        // The function queries the database for a user with the given ID, 
        // if the user is found the fetched row is passed to the constructor to create a new User object 
        // The constructor receives the parameters as associative arrays, and assigns them to the object properties
        
        global $configs;
        $rows = run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_USERS_TABLE WHERE id = '$id'");
        return $rows->num_rows > 0 ? new User($rows->fetch_assoc()) : null;
    }
    // Creates and returns a User object given an email and an md5 hash for a password if the user exists, otherwise null
    static public function get_by_email_and_password_hash($email, $md5Hash): User|null
    {
        global $configs;
        $rows = run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_USERS_TABLE WHERE email = '$email' AND passwordHash = '$md5Hash'");
        return $rows->num_rows > 0 ? new User($rows->fetch_assoc()) : null;
    }
    // Creates 
    static public function create_new_user($userData): bool
    {
        global $configs;
        // implode: joins array elements into a single string
        $columns = implode(", ", array_keys($userData));
        $values = implode("', '", array_values($userData));
        $query = "INSERT INTO $configs->DB_NAME.$configs->DB_USERS_TABLE ($columns) VALUES ('$values')";
        return run_query($query);
    }
    // Deletes a user from the database given an ID, returns true if successful, otherwise false
    static public function delete_by_id($id): bool
    {
        global $configs;
        $query = "DELETE FROM $configs->DB_NAME.$configs->DB_USERS_TABLE WHERE id = '$id'";
        return run_query($query);
    }
    // Updates a user's information in the database given an ID and an associative array of new data, returns true if successful, otherwise false
    static public function update_by_id($id, $newData): bool
    {
        global $configs;
        $setClause = [];
        foreach ($newData as $column => $value) {
            $setClause[] = "$column = '$value'";
        }
        $setClauseString = implode(", ", $setClause);
        $query = "UPDATE $configs->DB_NAME.$configs->DB_USERS_TABLE SET $setClauseString WHERE id = '$id'";
        return run_query($query);
    }

}
?>