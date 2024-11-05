<?php

declare(strict_types=1);

ob_start();
require_once "./db_setup.php";
ob_end_clean();

// Properties: id, firstName, lastName, email, passwordHash (MD5)

class User
{
    /* ------------------- Attributes -------------------  */
    private int $id;
    private int $type;
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $passwordHash;

    /* ------------------- Constructor and toString -------------------  */
    private function __construct(array $properties)
    {
        foreach ($properties as $prop => $value) {
            if (property_exists($this, $prop)) {
                $this->{$prop} = $value;
            }
        }
    }

    public function get_id(): int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        $str = '<pre>';
        foreach ($this as $key => $value) {
            $str .= "$key: $value<br/>";
        }
        return $str . '</pre>';
    }

    /* ------------------- Getters and Setters -------------------  */
    // Getter for id
    public function getId(): int
    {
        return $this->id;
    }

    // Getter for firstName
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    // Getter for lastName
    public function getLastName(): string
    {
        return $this->lastName;
    }

    // Getter for email
    public function getEmail(): string
    {
        return $this->email;
    }

    // Getter for passwordHash
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }
    public function getType(): int
    {
        return $this->type;
    }   
    public static function create(array $data): User {
        return new self($data);
    }

    /* ------------------- Static Database Manipulation Functions -------------------  */
    // Creates and returns a User object given an ID if the user exists, otherwise null
    static public function get_by_id(int $id): ?User
    {
        global $configs;
        $rows = run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_USERS_TABLE WHERE id = '$id'");
        return $rows->num_rows > 0 ? new User($rows->fetch_assoc()) : null;
    }

    // Creates and returns a User object given an email and an md5 hash for a password if the user exists, otherwise null
    static public function get_by_email_and_password_hash(string $email, string $md5Hash): ?User
    {
        global $configs;
        $rows = run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_USERS_TABLE WHERE email = '$email' AND passwordHash = '$md5Hash'");
        return $rows->num_rows > 0 ? new User($rows->fetch_assoc()) : null;
    }

    // Checks if a user with a given email exists, returns true if they do, otherwise false
    static public function does_email_exist(string $email): bool
    {
        global $configs;
        $rows = run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_USERS_TABLE WHERE email = '$email'");
        return $rows->num_rows > 0;
    }

    // Creates a new user in the database given an associative array of user data, returns true if successful, otherwise false
    static public function create_new_user(array $userData): bool
    {
        global $configs;
        $columns = implode(", ", array_keys($userData));
        $values = implode("', '", array_values($userData));
        $query = "INSERT INTO $configs->DB_NAME.$configs->DB_USERS_TABLE ($columns) VALUES ('$values')";
        return run_query($query);
    }

    // Deletes a user from the database given an ID, returns true if successful, otherwise false
    static public function delete_by_id(int $id): bool
    {
        global $configs;
        $query = "DELETE FROM $configs->DB_NAME.$configs->DB_USERS_TABLE WHERE id = '$id'";
        return run_query($query);
    }

    // Updates a user's information in the database given an ID and an associative array of new data, returns true if successful, otherwise false
    static public function update_by_id(int $id, array $newData): bool
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
