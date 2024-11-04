<?php

declare(strict_types=1);

ob_start();
require_once "./db_setup.php";
ob_end_clean();

class Event
{
    // Define properties
    public int $id;
    public string $name;
    public string $description;
    public string $location;
    public string $type;
    public string $date;

    // Constructor that initializes properties with type casting
    private function __construct(array $properties)
    {
        $this->id = (int)($properties['id'] ?? 0); // Cast to int
        $this->name = $properties['name'] ?? '';
        $this->description = $properties['description'] ?? '';
        $this->location = $properties['location'] ?? '';
        $this->type = $properties['type'] ?? '';
        $this->date = $properties['date'] ?? '';
    }

    public function __toString(): string
    {
        $str = '<pre>';
        foreach ($this as $key => $value) {
            $str .= "$key: $value<br/>";
        }
        return $str . '</pre>';
    }

    static public function get_by_id(int $id): ?Event
    {
        global $configs;
        $rows = run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_EVENTS_TABLE WHERE id = ?", [$id]);
        return $rows && $rows->num_rows > 0 ? new Event($rows->fetch_assoc()) : null;
    }

    // Get every event
    static public function get_all(): array
    {
        $events = [];
        global $configs;    
        $rows = run_select_query("SELECT * FROM $configs->DB_EVENTS_TABLE")->fetch_all(MYSQLI_ASSOC);

        foreach ($rows as $row) {
            $events[] = new Event($row); // Corrected variable name
        }
        return $events;
    }

    // Add an event
    static public function add_event(string $name, string $description, string $location, string $type, string $date): bool
    {
        // Check if event already exists
        global $configs;    
        $checkEvent = run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_EVENTS_TABLE WHERE `name` = ?", [$name], true);
        
        // Use num_rows to check if there are no results
        if ($checkEvent && $checkEvent->num_rows == 0) {  
            // Insert the new event
            return run_query("INSERT INTO $configs->DB_NAME.$configs->DB_EVENTS_TABLE (`name`, `description`, `location`, `type`, `date`) VALUES (?, ?, ?, ?, ?)", 
                [$name, $description, $location, $type, $date], true);
        }
    
        return false; // Return false if event already exists
    }

    // Delete event
    static public function delete_event(int $id): bool
    {
        // Check if event exists
        global $configs;
        $result = run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_EVENTS_TABLE WHERE `id` = $id");

        if ($result && $result->num_rows > 0) {
            // Remove the event
            $success = run_query("DELETE FROM $configs->DB_NAME.$configs->DB_EVENTS_TABLE WHERE `id` = $id");

            if (!$success) {
                error_log("Database delete failed..."); // Log the error
            }

            return $success;
        }

        return false; // Return false if event does not exist
    }
}