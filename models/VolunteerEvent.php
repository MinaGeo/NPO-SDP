<?php

declare(strict_types=1);

ob_start();
require_once "./db_setup.php";
ob_end_clean();
require_once "./models/userBase.php";

class VolunteerEvent
{
    public int $id;
    public int $volunteer_id;
    public int $event_id;

    private function __construct(array $properties)
    {
        $this->id = (int)($properties['id'] ?? 0);
        $this->volunteer_id = (int)($properties['volunteer_id'] ?? 0);
        $this->event_id = (int)($properties['event_id'] ?? 0);
    }

    public function __toString(): string
    {
        $str = '<pre>';
        foreach ($this as $key => $value) {
            $str .= "$key: $value<br/>";
        }
        return $str . '</pre>';
    }

    public static function register(int $volunteerId, int $eventId): bool
    {
        global $configs;

        $checkRegistration = run_select_query(
            "SELECT * FROM $configs->DB_NAME.volunteer_events WHERE volunteer_id = ? AND event_id = ?",
            [$volunteerId, $eventId],
            true
        );

        if ($checkRegistration && $checkRegistration->num_rows == 0) {
            return run_query(
                "INSERT INTO $configs->DB_NAME.volunteer_events (volunteer_id, event_id) VALUES (?, ?)",
                [$volunteerId, $eventId],
                true
            );
        }

        return false;
    }

    public static function get_events_by_volunteer(int $volunteerId): array
    {
        $events = [];
        global $configs;

        // echo "Volunteer ID: " . $volunteerId . "<br>";

        // Query to get events related to a specific volunteer by filtering with volunteer_id
        $dbName = $configs->DB_NAME;
        $query = "
            SELECT e.id, e.name, e.description, e.location, e.type, e.date
            FROM {$dbName}.event e
            JOIN {$dbName}.volunteer_events ve ON e.id = ve.event_id
            WHERE ve.volunteer_id = ?
        ";

        // Fetch results
        $stmt = run_select_query($query, [$volunteerId]);

        if ($stmt && $stmt instanceof mysqli_result) {
            $rows = $stmt->fetch_all(MYSQLI_ASSOC);

            // // Debugging
            // echo "Rows returned: " . count($rows) . "<br>";

            // if (count($rows) > 0) {
            //     var_dump($rows);
            // } else {
            //     echo "No events found for this volunteer.<br>";
            // }

            // create Event objects using the (factory method)
            foreach ($rows as $row) {
                $events[] = Event::create($row);
            }
        } else {
            echo "No valid result returned or query failed.<br>";
        }

        return $events;
    }

    public static function get_volunteers_by_event(int $eventId): array
    {
        $volunteers = [];
        global $configs;
    
        // Query to get volunteers associated with a specific event by filtering with event_id
        $dbName = $configs->DB_NAME;
        $query = "
            SELECT v.id, v.firstName, v.email
            FROM {$dbName}.user v
            JOIN {$dbName}.volunteer_events ve ON v.id = ve.volunteer_id
            WHERE ve.event_id = ?
        ";
    
        // Fetch results
        $stmt = run_select_query($query, [$eventId]);
    
        if ($stmt && $stmt instanceof mysqli_result) {
            $rows = $stmt->fetch_all(MYSQLI_ASSOC);
    
            // Create Volunteer objects or populate the array directly
            foreach ($rows as $row) {
                $volunteers[] = User::create($row);  // Assuming a User::create factory method
            }
        } else {
            echo "No volunteers found for this event or query failed.<br>";
        }
    
        return $volunteers;
    }
    

    static public function removeVolunteerFromEvent(int $volunteerId, int $eventId): bool
    {
        global $configs;
        $result = run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_VOLUNTEER_EVENTS_TABLE WHERE volunteer_id = $volunteerId AND event_id = $eventId");
        if ($result && $result->num_rows > 0) {
            $success = run_query("DELETE FROM $configs->DB_NAME.$configs->DB_VOLUNTEER_EVENTS_TABLE WHERE volunteer_id = $volunteerId AND event_id = $eventId");

            if (!$success) {
                error_log("Database delete failed...");
            }

            return $success;
        }
        return false;
    }

    // Optional: Retrieve all volunteer-event registrations (if needed)
    public static function get_all(): array
    {
        $volunteerEvents = [];
        global $configs;

        // Fetch all volunteer-event records
        $rows = run_select_query(
            "SELECT * FROM $configs->DB_NAME.volunteer_events",
            [],
            true
        )->fetch_all(MYSQLI_ASSOC);

        foreach ($rows as $row) {
            $volunteerEvents[] = new VolunteerEvent($row); // Create VolunteerEvent objects
        }

        return $volunteerEvents;
    }

    // Delete a volunteer registration for an event
    public static function delete_registration(int $volunteerId, int $eventId): bool
    {
        global $configs;

        // Check if the registration exists
        $checkRegistration = run_select_query(
            "SELECT * FROM $configs->DB_NAME.volunteer_events WHERE volunteer_id = ? AND event_id = ?",
            [$volunteerId, $eventId],
            true
        );

        if ($checkRegistration && $checkRegistration->num_rows > 0) {
            // Delete the registration
            return run_query(
                "DELETE FROM $configs->DB_NAME.volunteer_events WHERE volunteer_id = ? AND event_id = ?",
                [$volunteerId, $eventId],
                true
            );
        }

        return false; // No such registration found
    }
}
