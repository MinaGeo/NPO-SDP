<?php
include '../models/IFilter.php';
include '../models/FilterByCategoryStrategy.php';
include '../models/FilterByPriceStrategy.php';
include '../models/FilterByRatingStrategy.php';
include '../models/FilteringContext.php';

class FilterController {
    private FilteringContext $context;

    public function __construct() {
        $this->context = new FilteringContext();
    }

    public function filterData(array $data, IFilter $strategy): array {
        $this->context->setStrategy($strategy);
        return $this->context->filterData($data);
    }

    public function displayFilteredData(array $data, IFilter $strategy): void {
        $filteredData = $this->filterData($data, $strategy);
        include '../views/filter_view.php';//----------------------------------------------> Include the View (RAFIK)
    }
}

// Instantiate the controller and call the method --------------------------------> Change Data (Get From DB)
$controller = new FilterController();
$data = [
    ["name" => "Product A", "category" => "Electronics", "price" => 50, "rating" => 4.2],
    ["name" => "Product test", "category" => "Electronics", "price" => 150, "rating" => 3.2],
    ["name" => "Product B", "category" => "Books", "price" => 30, "rating" => 3.8],
];

// Filter and display data
$controller->displayFilteredData($data, new FilterByCategoryStrategy("Electronics"));
?>