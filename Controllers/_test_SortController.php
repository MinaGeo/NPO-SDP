<?php 
include __DIR__.'/../models/ISort.php';
include __DIR__.'/../models/SortStrategy.php';
include __DIR__.'/../models/SortingContext.php';
include __DIR__.'/../EventModel.php';

class SortController {
    private SortingContext $context;

    public function __construct() {
        $this->context = new SortingContext();
    }

    public function sortData(array $data, ISort $strategy): array {
        $this->context->setStrategy($strategy);
        return $this->context->sortData($data);
    }

    public function displaySortedData(): void {
        $events = Event::get_all();
        
        // Determine sorting strategy based on query parameter
        $sortType = $_GET['eventSort'] ?? 'name_asc';
        switch ($sortType) {
            case 'name_asc':
                $strategy = new SortByNameAscStrategy();
                break;
            case 'name_desc':
                $strategy = new SortByNameDescStrategy();
                break;
            case 'date_asc':
                $strategy = new SortByDateAscStrategy();
                break;
            case 'date_desc':
                $strategy = new SortByDateDescStrategy();
                break;
            default:
                $strategy = new SortByNameAscStrategy();
                break;
        }

        // Sort data and require_once the view
        $events = $this->sortData($events, $strategy);
        // require_once 'models/EventView.php';
    }
}

// Instantiate the controller and display sorted data
$controller = new SortController();
$controller->displaySortedData();
?>
