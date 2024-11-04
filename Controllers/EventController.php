<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__."/../models/EventModel.php";
include __DIR__.'/../models/IFilter.php';
include __DIR__.'/../models/FilterStrategy.php';
include __DIR__.'/../models/FilteringContext.php';
include __DIR__.'/../models/ISort.php';
include __DIR__.'/../models/SortStrategy.php';
include __DIR__.'/../models/SortingContext.php';

class EventController
{
    private FilteringContext $filteringContext;
    private SortingContext $sortingContext;

    public function __construct()
    {
        $this->filteringContext = new FilteringContext();
        $this->sortingContext = new SortingContext();
    }
    public function show($eventFilter = 'event_type', $eventType = '', $eventSort = 'name_asc')
    {
        // Retrieve all events
        $events = Event::get_all();
    
        // Handle Filtering based on the filter type
        switch ($eventFilter) {
            case 'event_type':
                // Use the passed eventType query parameter for filtering
                $filterStrategy = new FilterByEventTypeStrategy($eventType); 
                break;
            default:
                // Default filter strategy if none specified
                $filterStrategy = new FilterByEventTypeStrategy(); 
                break;
        }
    
        // Apply the selected filter strategy
        $this->filteringContext->setStrategy($filterStrategy);
        $events = $this->filteringContext->filterData($events);
    
        // Handle Sorting based on the sort type
        switch ($eventSort) {
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
                // Default sorting strategy if none specified
                $sortStrategy = new SortByNameAscStrategy();
                break;
        }
    
        // Apply the selected sorting strategy
        $this->sortingContext->setStrategy($sortStrategy);
        $events = $this->sortingContext->sortData($events);
    
        // Pass filtered and sorted events to the view
        require_once "./views/EventView.php";
    }
    
    public function showAddEvent()
    {
        echo "Entering showADd";
        require_once "./views/addEventView.php";
    }


    public function deleteEvent()
    {
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
    }



    public function addNewEvent()
    {
        if (isset($_POST['addEvent'])) {
            if (Event::add_event($_POST['name'], $_POST['description'], $_POST['location'], $_POST['type'], $_POST['date'])) {
                echo json_encode(['success' => true, 'message' => $_POST['name'] . 'Event Added!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add Event or Event already exist.']);
            }
            exit;
        }
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
