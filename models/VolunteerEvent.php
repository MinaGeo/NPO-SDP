<?php

declare(strict_types=1);

ob_start();
require_once "./db_setup.php";
ob_end_clean();
require_once "./models/userBase.php";
require_once "itemIterator.php";
require_once "./models/IAggregater.php";

class VolunteerEvent implements IAggregater
{
    private int $id;
    private int $volunteer_id;
    private int $event_id;

    public function get_id(): int
    {
        return $this->id;
    }
    public function get_volunteer_id(): int
    {
        return $this->volunteer_id;
    }
    public function get_event_id(): int
    {
        return $this->event_id;
    }


    private function __construct(array $properties)
    {
        $this->id = (int)($properties['id'] ?? 0);
        $this->volunteer_id = (int)($properties['volunteer_id'] ?? 0);
        $this->event_id = (int)($properties['event_id'] ?? 0);
    }


    public function getIterator($items): itemIterator
    {
        return new itemIterator($items);
    }

    public function __toString(): string
    {
        global $configs;
        global $conn;
        $dbName = $configs->DB_NAME;
        $str = '<br/>';
        $query = "
        SELECT e.id, e.name, e.description, e.location, e.type, e.date
        FROM {$dbName}.event e
        JOIN {$dbName}.volunteer_events ve ON e.id = ve.event_id
        WHERE ve.volunteer_id = ?
    ";

        // Fetch results

        $stmt = $conn->run_select_query($query, [$this->volunteer_id]);

        if ($stmt && $stmt instanceof mysqli_result) {
            $rows = $stmt->fetch_all(MYSQLI_ASSOC);
            $iterator = $this->getIterator($rows);
        }

        while ($iterator->hasNext()) {
            $key = $iterator->currentKey(); // Get the current key
            $value = $iterator->current();  // Get the current value
            $str .= "    Item ID #$key: Qty $value<br/>";
            $iterator->next(); // Move to the next item
        }

        return $str;
    }


    public static function register(int $volunteerId, int $eventId): bool
    {
        global $configs;
        global $conn;
        $checkRegistration = $conn->run_select_query(
            "SELECT * FROM $configs->DB_NAME.volunteer_events WHERE volunteer_id = ? AND event_id = ?",
            [$volunteerId, $eventId],
            true
        );

        if ($checkRegistration && $checkRegistration->num_rows == 0) {
            return $conn->run_query(
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
        global $conn;
        // echo "Volunteer ID: " . $volunteerId . "<br>";

        // Query to get events related to a specific volunteer by filtering with volunteer_id
        $dbName = $configs->DB_NAME;
        $query = " 
            SELECT e.id, e.name, e.description, lh.id as location_id, e.type, e.date, pl.name as parent_location, cl.name as child_location 
            FROM {$dbName}.event e 
            JOIN {$dbName}.volunteer_events ve ON e.id = ve.event_id 
            JOIN {$dbName}.location_hierarchy lh ON e.location_id = lh.id 
            JOIN {$dbName}.location pl ON lh.parent_id = pl.id 
            JOIN {$dbName}.location cl ON lh.child_id = cl.id WHERE ve.volunteer_id = ? ";

        // Fetch results
        $stmt = $conn->run_select_query($query, [$volunteerId]);

        if ($stmt && $stmt instanceof mysqli_result) {
            $rows = $stmt->fetch_all(MYSQLI_ASSOC);
            $eventVolunteerIterator = new itemIterator($rows);
            while ($eventVolunteerIterator->hasNext()) {
                  $row = $eventVolunteerIterator->next();
                  $row['location'] = $row['child_location'] . ", " . $row['parent_location'];
                $event = Event::create($row);
                $events[] = $event;
            }
        } else {
            echo "No valid result returned or query failed.<br>";
        }

        return $events;
    }


    // This function is not used in the current implementation. It is kept for future use.
    public static function get_volunteers_by_event(int $eventId): array
    {
        $volunteers = [];
        global $configs;
        global $conn;

        // Query to get volunteers associated with a specific event by filtering with event_id
        $dbName = $configs->DB_NAME;
        $query = "
            SELECT v.id, v.firstName, v.email
            FROM {$dbName}.user v
            JOIN {$dbName}.volunteer_events ve ON v.id = ve.volunteer_id
            WHERE ve.event_id = ?
        ";

        // Fetch results
        $stmt = $conn->run_select_query($query, [$eventId]);

        if ($stmt && $stmt instanceof mysqli_result) {
            $rows = $stmt->fetch_all(MYSQLI_ASSOC);

            $eventVolunteerIterator = new itemIterator($rows);
            while ($eventVolunteerIterator->hasNext()) {
                $volunteer = User::create($eventVolunteerIterator->next());
                $volunteers[] = $volunteer;
            }
        } else {
            echo "No volunteers found for this event or query failed.<br>";
        }

        return $volunteers;
    }


    static public function removeVolunteerFromEvent(int $volunteerId, int $eventId): bool
    {
        global $configs;
        global $conn;
        $result = $conn->run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_VOLUNTEER_EVENTS_TABLE WHERE volunteer_id = $volunteerId AND event_id = $eventId");
        if ($result && $result->num_rows > 0) {
            $success = $conn->run_query("DELETE FROM $configs->DB_NAME.$configs->DB_VOLUNTEER_EVENTS_TABLE WHERE volunteer_id = $volunteerId AND event_id = $eventId");

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
        global $conn;
        // Fetch all volunteer-event records
        $rows = $conn->run_select_query(
            "SELECT * FROM $configs->DB_NAME.volunteer_events",
            [],
            true
        )->fetch_all(MYSQLI_ASSOC);

        $volunteerEventIterator = new itemIterator($rows);
        while ($volunteerEventIterator->hasNext()) {
            $volunteerEvent = new VolunteerEvent($volunteerEventIterator->next());
            $volunteerEvents[] = $volunteerEvent;
        }
        return $volunteerEvents;
    }

    // Delete a volunteer registration for an event
    public static function delete_registration(int $volunteerId, int $eventId): bool
    {
        global $configs;
        global $conn;

        // Check if the registration exists
        $checkRegistration = $conn->run_select_query(
            "SELECT * FROM $configs->DB_NAME.volunteer_events WHERE volunteer_id = ? AND event_id = ?",
            [$volunteerId, $eventId],
            true
        );

        if ($checkRegistration && $checkRegistration->num_rows > 0) {
            // Delete the registration
            return $conn->run_query(
                "DELETE FROM $configs->DB_NAME.volunteer_events WHERE volunteer_id = ? AND event_id = ?",
                [$volunteerId, $eventId],
                true
            );
        }

        return false; // No such registration found
    }
}
