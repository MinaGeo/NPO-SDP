<?php

declare(strict_types=1);

ob_start();
require_once "./db_setup.php";
ob_end_clean();
require_once "models/itemIterator.php";
require_once "LocationComponent.php";
require_once "SingleLocation.php";
require_once "CompositeLocation.php";
require_once "LocationRepository.php"; // Include the LocationRepository
class Event
{
    // Define properties
    private int $id;
    private string $name;
    private string $description;
    private int $location_id;
    private string $type;
    private string $date;


    public function get_id(): int
    {
        return $this->id;
    }
    public function get_name(): string
    {
        return $this->name;
    }
    public function get_description(): string
    {
        return $this->description;
    }
    public function get_location(): string
    {
        return LocationRepository::getLocationHierarchy($this->location_id);
    }   
    public function get_type(): string
    {
        return $this->type;
    }
    public function get_date(): string
    {
        return $this->date;
    }

    // Constructor that initializes properties with type casting
    private function __construct(array $properties)
    {
        $this->id = (int)($properties['id'] ?? 0); // Cast to int
        $this->name = $properties['name'] ?? '';
        $this->description = $properties['description'] ?? '';
        $this->location_id = (int)($properties['location_id'] ?? 0); // Store location ID
        $this->type = $properties['type'] ?? '';
        $this->date = $properties['date'] ?? '';
    }

    public function getIterator(): itemIterator
    {
        return new itemIterator([
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'location' => $this->get_location(),
            'type' => $this->type,
            'date' => $this->date
        ]);
    }

    public function __toString(): string
    {
        $str = '<pre>';
        $iterator = $this->getIterator(); // Assuming getIterator() returns a ShopIterator
        while ($iterator->hasNext()) {
            $key = $iterator->currentKey();
            $value = $iterator->current();
            $str .= "$key: $value<br/>";
            $iterator->next();
        }
        return $str . '</pre>';
    }
    

    // RAFIK----> To Use with volunteer's Tables
    public static function create(array $data): Event {
        return new self($data);
    }

    static public function get_by_id(int $id): ?Event
    {   
        global $configs;
        global $conn;
        $rows = $conn->run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_EVENTS_TABLE WHERE id = ?", [$id]);
        return $rows && $rows->num_rows > 0 ? new Event($rows->fetch_assoc()) : null;
    }

    // Get every event
    static public function get_all(): array
    {
        $events = [];
        global $configs; 
        global $conn;   
        $rows = $conn->run_select_query("SELECT * FROM $configs->DB_EVENTS_TABLE")->fetch_all(MYSQLI_ASSOC);

        $eventIterator = new itemIterator($rows);
        while ($eventIterator->hasNext()) {
            $event = Event::create($eventIterator->next());
            $events[] = $event;
        }
        return $events;
    }

    // Add an event
    static public function add_event(string $name, string $description, int $location_id, string $type, string $date): bool
    {
        // Check if event already exists
        global $configs;    
        global $conn;
        $checkEvent = $conn->run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_EVENTS_TABLE WHERE `name` = ?", [$name], true);
        
        // Use num_rows to check if there are no results
        if ($checkEvent && $checkEvent->num_rows == 0) {  
            // Insert the new event
            return $conn->run_query("INSERT INTO $configs->DB_NAME.$configs->DB_EVENTS_TABLE (`name`, `description`, `location_id`, `type`, `date`) VALUES (?, ?, ?, ?, ?)", 
                [$name, $description, $location_id, $type, $date], true);
        }
    
        return false; // Return false if event already exists
    }

    // Delete event
    static public function delete_event(int $id): bool
    {
        // Check if event exists
        global $configs;
        global $conn;
        $result = $conn->run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_EVENTS_TABLE WHERE `id` = $id");

        if ($result && $result->num_rows > 0) {
            // Remove the event
            $success = $conn->run_query("DELETE FROM $configs->DB_NAME.$configs->DB_EVENTS_TABLE WHERE `id` = $id");

            if (!$success) {
                error_log("Database delete failed..."); // Log the error
            }

            return $success;
        }

        return false; // Return false if event does not exist
    }
}