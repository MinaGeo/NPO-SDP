<?php
require_once 'models\IFilter.php';
require_once 'models/FilterStrategy.php';
require_once 'models/FilteringContext.php';
require_once 'models/EventModel.php'; // require_once your Event model

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

        // Filter data and require_once the view
        $events = $this->filterData($events, $strategy);
        // require_once 'models/EventView.php'; // require_once the view with filtered events
    }
}

// Instantiate the controller and call the method
$controller = new FilterController();
$controller->displayFilteredData();
?>