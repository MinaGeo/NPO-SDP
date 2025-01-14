<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "./models/shop/ShopModel.php";
require_once './models/filter/IFilter.php';
require_once './models/filter/FilterStrategy.php';
require_once './models/filter/FilteringContext.php';
require_once './models/sort/ISort.php';
require_once './models/sort/SortStrategy.php';
require_once './models/sort/SortingContext.php';
require_once "./models/cart/CartInvoker.php";
require_once "./models/cart/AddItemToCartCommand.php";
require_once "./models/cart/CartModel.php";
require_once "./views/ShopView.php";

class ShopController implements IControl
{
    private SortingContext $sortingContext;
    private $shopView;

    public function __construct()
    {
        $this->shopView = new shopView();
        $this->sortingContext = new SortingContext();
    }

    public function show($itemSort = 'name_asc')
    {
        $userType = $_SESSION['USER_TYPE'];
        $userId = $_SESSION['USER_ID'];
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

        $this->shopView->showUserPage($shop_items, $userType, $userId);
    }

    public function showAddItem()
    {
        $this->shopView->shopAddItemPage();
    }

    public function shopDeleteItem()
    {
        if (isset($_POST['deleteItem'])) {
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
        }
    }

    public function shopAddItem()
    {
        if (isset($_POST['addItem'])) {
            if (ShopItem::add_shop_item($_POST['name'], $_POST['description'], $_POST['price'])) {
                echo json_encode(['success' => true, 'message' => $_POST['name'] . ' Item Added!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add item or item already exists.']);
            }
            exit;
        }
    }


    public function shopAddItemToCart()
    {
        // Ensure there's a "current" cart for the user
        $cart = Cart::get_current_cart_by_user_id($_SESSION['USER_ID']);

        if (!$cart) {
            // If no current cart exists, create a new one
            Cart::create_new_cart($_SESSION['USER_ID']);
            $cart = Cart::get_current_cart_by_user_id($_SESSION['USER_ID']);
        }

        if (isset($_POST['addToCart'])) {
            if (!empty($_SESSION['USER_ID']) && !empty($_POST['itemId'])) {
                //$result = Cart::add_item_to_cart($cart->get_id(), $_POST['itemId']);
                
                $itemId = $_POST['itemId'];
                
                // Set up invoker
                $invoker = new CartInvoker();

                // Create add item command and assign it to the invoker's "on" method
                $addItemCommand = new AddItemToCartCommand($cart->get_id(), $itemId);
                $invoker->setOnCommand($addItemCommand);

                // Execute "on" to add the item to the cart
                $result = $invoker->on();
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Item added to cart!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to add item to cart.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid input.']);
            }
            exit; // Ensure no further output is sent
        }
    }
}
