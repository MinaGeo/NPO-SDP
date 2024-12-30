<?php

declare(strict_types=1);

ob_start();
require_once "./db_setup.php";
ob_end_clean();
require_once "./models/userBase.php";
require_once "itemIterator.php";
class VolunteerEvent
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
        $dbName = $configs->DB_NAME;
        $str = '<br/>';
        $query = "
        SELECT e.id, e.name, e.description, e.location, e.type, e.date
        FROM {$dbName}.event e
        JOIN {$dbName}.volunteer_events ve ON e.id = ve.event_id
        WHERE ve.volunteer_id = ?
    ";

        // Fetch results
        $stmt = run_select_query($query, [$this->volunteer_id]);

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

            $eventVolunteerIterator = new itemIterator($rows);
            while ($eventVolunteerIterator->hasNext()) {
                $event = Event::create($eventVolunteerIterator->next());
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
