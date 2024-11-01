<?php
include '../models/IFilter.php';
include '../models/FilterStrategy.php';
include '../models/FilteringContext.php';
include '../EventModel.php'; // Include your Event model

class FilterController {
    private FilteringContext $context;

    public function __construct() {
        $this->context = new FilteringContext();
    }

    public function filterData(array $data, IFilter $strategy): array {
        $this->context->setStrategy($strategy);
        return $this->context->filterData($data);
    }

    public function displayFilteredData(): void {
        $events = Event::get_all(); // Fetch all events from the database

        // Determine filtering strategy based on query parameter
        $filterType = $_GET['eventFilter'] ?? 'event_type'; // Get the filter type
        switch ($filterType) {
            case 'event_type':
                $strategy = new FilterByEventTypeStrategy($_GET['eventType'] ?? ''); // Pass the filter criteria
                break;
            default:
                $strategy = new FilterByEventTypeStrategy(); // Default strategy
                break;
        }

        // Filter data and include the view
        $events = $this->filterData($events, $strategy);
        include '../EventView.php'; // Include the view with filtered events
    }
}

// Instantiate the controller and call the method
$controller = new FilterController();
$controller->displayFilteredData();
?>
