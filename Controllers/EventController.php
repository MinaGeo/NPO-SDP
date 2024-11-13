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
require_once "./models/IEventSubject.php";

class EventController implements IObservable, IControl
{
    private FilteringContext $filteringContext;
    private SortingContext $sortingContext;
    private $users;

    public function __construct()
    {
        $this->filteringContext = new FilteringContext();
        $this->sortingContext = new SortingContext();
        $this->users = [];
    }
    public function show($eventFilter = '', $eventType = '', $eventSort = 'name_asc', $location = '')
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
            case 'location':
                // Use the passed location query parameter for filtering
                $filterStrategy = new FilterByLocationStrategy($location);
                break;
            default:
                // Default filter strategy if none specified (using event type filter as default)
                $filterStrategy = new FilterByEventTypeStrategy();
                break;
        }

        // Apply the selected filter strategy
        $this->filteringContext->setStrategy($filterStrategy);
        $events = $this->filteringContext->filterData($events);  // Filter the events

        // Handle Sorting (as before)
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
        $events = $this->sortingContext->sortData($events);  // Sort the events

        // Render the view based on user type
        require_once "./views/Navbar.php";

        if ((int)$_SESSION['USER_ID'] === -1) {
            require_once "./views/EventViewGuest.php";
        } else {
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
        }
    }



    public function showAddEvent()
    {
        //echo "Entering showADd";
        require_once "./views/Navbar.php";
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
            $this->users = VolunteerEvent::get_volunteers_by_event($eventId);
            // echo "registering";
            // foreach ($this->users as $user) {
            //     $this->attach($user);
            // }
            $this->notifyUsers("User with ID: $volunteerId joined event with ID: $eventId");
            $registered = VolunteerEvent::register($volunteerId, $eventId);
            if ($registered) {
                json_encode(['success' => true, 'message' => 'Successfully registered for the event!', "notifications" => $this->users]);
            } else {
                json_encode(['success' => false, 'message' => 'Failed to register for the event.']);
            }
            exit;
        }
    }

    public function showVolunteerEvents($eventFilter = 'event_type', $eventType = '', $eventSort = 'name_asc')
    {
        $volunteerId = $_SESSION['USER_ID'];
        $volunteerEvents = VolunteerEvent::get_events_by_volunteer($volunteerId);

        // Handle filtering based on the selected filter type
        switch ($eventFilter) {
            case 'event_type':
                $filterStrategy = new FilterByEventTypeStrategy($eventType);
                break;
            case 'location':
                // Retrieve location from query parameter
                $location = $_GET['location'] ?? '';
                $filterStrategy = new FilterByLocationStrategy($location);
                break;
            default:
                // Default filter strategy if none specified
                $filterStrategy = new FilterByEventTypeStrategy();
                break;
        }

        $this->filteringContext->setStrategy($filterStrategy);
        $volunteerEvents = $this->filteringContext->filterData($volunteerEvents);  // Apply filtering

        // Handle sorting (unchanged)
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
        $volunteerEvents = $this->sortingContext->sortData($volunteerEvents);  // Apply sorting

        // Render the view
        require_once "./views/Navbar.php";
        require_once "./views/myEventsView.php";
    }


    public function attach(IMEssagable $messagable): void
    {
        $name = $messagable->getFirstName();
        echo "Event: Adding User: $name .</br>";
        $this->users[] = $messagable;
    }

    public function detach(int $idx)
    {
        // Let's say we want to remove the element at index 2
        unset($this->users[$idx]);

        // To reindex the array after removal
        $this->users = array_values($this->users);
    }

    public function notifyUsers(string $msg): void
    {
        $size = count($this->users);
        echo "Notifying Users $size...</br>";
        foreach ($this->users as $user) {
            $user->sendnotification($msg);
        }
    }
}
