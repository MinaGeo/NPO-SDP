<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "./models/EventModel.php";
require_once './models/VolunteerEvent.php';
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

    public function __construct()
    {
        $this->filteringContext = new FilteringContext();
        $this->sortingContext = new SortingContext();
    }
    public function show($eventFilter = 'event_type', $eventType = '', $eventSort = 'name_asc')
    {

        $volunteerId = $_SESSION['USER_ID'];
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
        //echo $usertype;
        echo $_SESSION['USER_ID'];
        echo $_SESSION['USER_TYPE'];
        // Pass filtered and sorted events to the view
        switch ((int)$_SESSION['USER_TYPE']) {
            case 0:
                require_once "./views/EventViewAdmin.php";
                break;

            case 1:
                require_once "./views/EventViewVolunteer.php";
                break;

            default:
                require_once "./views/EventViewGuest.php";
                break;
        }
        require_once "./views/Navbar.php";
        require_once "./views/EventView.php";
    }


    public function showAddEvent()
    {
        //echo "Entering showADd";
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

    public function removeMyEvent()
    {
        if (isset($_POST['removeMyEvent'])) {
            if (!empty($_POST['eventId']) && !empty($_SESSION['USER_ID'])) {
                $eventId = (int)$_POST['eventId'];
                $volunteerId = (int)$_SESSION['USER_ID'];

                $result = VolunteerEvent::removeVolunteerFromEvent($volunteerId, $eventId);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Successfully removed from the event.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to remove volunteer from the event or event not found.']);
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

    public function registerForEvent()
    {
        if (isset($_POST['registerEvent']) && !empty($_SESSION['USER_ID']) && !empty($_POST['event_id'])) {
            $volunteerId = (int)$_SESSION['USER_ID'];
            $eventId = (int)$_POST['event_id'];
            if (VolunteerEvent::register($volunteerId, $eventId)) {
                echo json_encode(['success' => true, 'message' => 'Successfully registered for the event!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to register for the event.']);
            }
            exit;
        }
    }

    public function showVolunteerEvents($eventFilter = 'event_type', $eventType = '', $eventSort = 'name_asc')
    {
        $usertype = $_SESSION['USER_TYPE'];
        $volunteerId = $_SESSION['USER_ID'];
        $volunteerEvents = VolunteerEvent::get_events_by_volunteer($_SESSION['USER_ID']);
        // echo "<br> Controller:<br> ";
        // print_r($volunteerEvents);

        switch ($eventFilter) {
            case 'event_type':
                $filterStrategy = new FilterByEventTypeStrategy($eventType);
                break;
            default:
                $filterStrategy = new FilterByEventTypeStrategy();
                break;
        }

        $this->filteringContext->setStrategy($filterStrategy);
        $volunteerEvents = $this->filteringContext->filterData($volunteerEvents);

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
                $sortStrategy = new SortByNameAscStrategy();
                break;
        }

        $this->sortingContext->setStrategy($sortStrategy);
        $volunteerEvents = $this->sortingContext->sortData($volunteerEvents);

        require_once "./views/myEventsView.php";
    }
}
