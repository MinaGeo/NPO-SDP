<?php 
include '../models/ISort.php';
include '../models/SortByNameAscStrategy.php';
include '../models/SortByNameDescStrategy.php';
include '../models/SortByPriceAscStrategy.php';
include '../models/SortByPriceDescStrategy.php';
include '../models/SortByRatingAscStrategy.php';
include '../models/SortByRatingDescStrategy.php';
include '../models/SortingContext.php';

class SortController {
    private SortingContext $context;

    public function __construct() {
        $this->context = new SortingContext();
    }

    public function sortData(array $data, ISort $strategy): array {
        $this->context->setStrategy($strategy);
        return $this->context->sortData($data);
    }

    public function displaySortedData(array $data, ISort $strategy): void {
        $sortedData = $this->sortData($data, $strategy);
        include '../views/sort_view.php'; //----------------------------------------------> Include the View (RAFIK)
    }
}

// Instantiate the controller and call the method --------------------------------> Change Data (Get From DB)
$controller = new SortController();
$data = [
    ["name" => "Product A", "price" => 50, "rating" => 4.2],
    ["name" => "Product B", "price" => 30, "rating" => 3.8],
];

// Sort and display data
$controller->displaySortedData($data, new SortByNameAscStrategy());
$controller->displaySortedData($data, new SortByNameDescStrategy());
$controller->displaySortedData($data, new SortByPriceAscStrategy());
$controller->displaySortedData($data, new SortByPriceDescStrategy());
$controller->displaySortedData($data, new SortByRatingAscStrategy());
$controller->displaySortedData($data, new SortByRatingDescStrategy());
?>