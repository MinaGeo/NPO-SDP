<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "./models/ShopModel.php";
include './models/IFilter.php';
include './models/FilterStrategy.php';
include './models/FilteringContext.php';
include './models/ISort.php';
include './models/SortStrategy.php';
include './models/SortingContext.php';

class ShopController
{
    private SortingContext $sortingContext;

    public function __construct()
    {
        $this->sortingContext = new SortingContext();
    }
    
    public function show($itemSort = 'name_asc')
    {
        // Retrieve all shop items
        $shop_items = ShopItem::get_all();

        // Handle Sorting based on the sort type
        switch ($itemSort) {
            case 'name_asc':
                $sortStrategy = new SortByNameAscStrategy();
                break;
            case 'name_desc':
                $sortStrategy = new SortByNameDescStrategy();
                break;
            case 'price_asc':
                $sortStrategy = new SortByPriceAscStrategy(); // Ensure this strategy exists
                break;
            case 'price_desc':
                $sortStrategy = new SortByPriceDescStrategy(); // Ensure this strategy exists
                break;
            default:
                // Default sorting strategy if none specified
                $sortStrategy = new SortByNameAscStrategy();
                break;
        }
    
        // Apply the selected sorting strategy
        $this->sortingContext->setStrategy($sortStrategy);
        $shop_items = $this->sortingContext->sortData($shop_items);
    
        // Pass filtered and sorted shop items to the view
        require_once "./views/ShopView.php";
    }
    public function showAddItem()
    {
        echo "Entering AddItemPage";
        require_once "./views/addShopItemView.php";
    }
    
    public function shopDeleteItem() {if (isset($_POST['deleteItem'])) {
        if (!empty($_POST['id'])) {
            $itemId = (int)$_POST['id'];
            if (ShopItem::delete_shop_item($itemId)) {
                echo json_encode(['success' => true, 'message' => 'Item deleted!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete item or item not found.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        }
        exit;
    }}
    

public function shopAddItem() {if (isset($_POST['addItem'])) {
    if (ShopItem::add_shop_item($_POST['name'], $_POST['description'], $_POST['price'])) {
        echo json_encode(['success' => true, 'message' => $_POST['name'] . ' Item Added!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add item or item already exists.']);
    }
    exit;
}}
}