<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "./models/EventModel.php";
require_once './models/IFilter.php';
require_once './models/FilterStrategy.php';
require_once './models/FilteringContext.php';
require_once './models/ISort.php';
require_once './models/SortStrategy.php';
require_once './models/SortingContext.php';

class EventController
{
    private FilteringContext $filteringContext;
    private SortingContext $sortingContext;

    public function __construct() {
        $this->filteringContext = new FilteringContext();
        $this->sortingContext = new SortingContext();
    }

    public function show() {
        $events = Event::get_all();

        // Handle Filtering
        $filterType = $_GET['eventFilter'] ?? 'event_type'; // Get the filter type
        switch ($filterType) {
            case 'event_type':
                $filterStrategy = new FilterByEventTypeStrategy($_GET['eventType'] ?? ''); // Pass the filter criteria
                break;
            default:
                $filterStrategy = new FilterByEventTypeStrategy(); // Default strategy
                break;
        }

        $this->filteringContext->setStrategy($filterStrategy);
        $events = $this->filteringContext->filterData($events);

        // Handle Sorting
        $sortType = $_GET['eventSort'] ?? 'name_asc';
        switch ($sortType) {
            case 'name_asc':
                $sortStrategy = new SortByNameAscStrategy();
                break;
            case 'name_desc':
                $sortStrategy = new SortByNameDescStrategy();
                break;
            case 'date_asc':
                $sortStrategy = new SortByDateAscStrategy();
                break;
            case 'date_desc':
                $sortStrategy = new SortByDateDescStrategy();
                break;
            default:
                $sortStrategy = new SortByNameAscStrategy();
                break;
        }

        $this->sortingContext->setStrategy($sortStrategy);
        $events = $this->sortingContext->sortData($events);

        // Make sure $events is available in the included file
        require_once "./views/EventView.php";
    }
}


$controller = new EventController();
$controller->show();




if (isset($_POST['deleteEvent'])) {
    if (!empty($_POST['id'])) {
        $eventId = (int)$_POST['id']; 
        if (Event::delete_event($eventId)) {
            echo json_encode(['success' => true, 'message' => 'Event deleted!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete Event or Event not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    }
    exit;
}





if (isset($_POST['addEvent'])) {
        if (Event::add_event($_POST['name'],$_POST['description'],$_POST['location'],$_POST['type'],$_POST['date'])) {
            echo json_encode(['success' => true, 'message' => $_POST['name'] .'Event Added!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add Event or Event already exist.']);
        }
    exit;
}
