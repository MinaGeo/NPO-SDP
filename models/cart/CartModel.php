<?php

declare(strict_types=1);

ob_start();
require_once "./db_setup.php";
ob_end_clean();
// require_once "./models/ShopItem.php";
require_once "CartDecorater.php";
require_once "./models/itemIterator.php";
class Cart implements Billable
{
    // Define properties
    private int $id;
    private int $user_id;
    private string $status;

    private $items = [];

    public function get_id(): int
    {
        return $this->id;
    }

    public function get_user_id(): int
    {
        return $this->user_id;
    }

    public function get_items(): array
    {
        return $this->items;
    }

    public function get_status(): string
    {
        return $this->status;
    }

    public function set_status(string $newStatus): void
    {
        $this->status = $newStatus;
    }


    public function getIterator($items): itemIterator
    {
        return new itemIterator($items);
    }

    // Constructor that initializes properties with type casting
    private function __construct(array $properties)
    {
        $this->id = (int)($properties['id']); // Cast to int
        $this->user_id = (int)($properties['user_id']); // Cast to int
        $this->status = $properties['status'];
    }

    public function __toString(): string
    {
        $str = '<br/>';
        $iterator = $this->getIterator($this->items);

        while ($iterator->hasNext()) {
            $key = $iterator->currentKey(); // Get the current key
            $value = $iterator->current();  // Get the current value
            $str .= "    Item ID #$key: Qty $value<br/>";
            $iterator->next(); // Move to the next item
        }

        return $str;
    }


    public static function cart_exists_for_user(int $user_id): bool
    {
        global $configs;

        // Correct SQL query using prepared statement syntax for safety
        $query = "SELECT COUNT(*) as cart_count FROM {$configs->DB_NAME}.{$configs->DB_CARTS_TABLE} WHERE user_id = $user_id";

        $result = run_select_query($query);

        if ($result && is_object($result)) {
            $row = $result->fetch_assoc();
            return $row['cart_count'] > 0; // Return true if there's at least one cart
        }

        return false; // Return false if no results or error
    }



    public function calc_price(): float
    {
        // Use the existing `get_total_cart_price()` for the base price
        return $this->get_total_cart_price();
    }


    public function get_total_cart_price(): float
    {
        $price = 0;
        $iterator = $this->getIterator($this->items);
        while ($iterator->hasNext()) {
            $item_id = $iterator->currentKey();
            $quantity = $iterator->current();
            $shopItem = ShopItem::get_by_id($item_id);
            $price += $quantity * $shopItem->get_price();
            $iterator->next();
        }
        return $price;
    }

    public function get_total_price_after_decoration(): float
    {
        // Wrap the cart with both VAT and Shipping decorators
        $decoratedCart = new ShippingDecorator(new VATDecorator($this));
        // Return the final decorated price
        return round($decoratedCart->calc_price(), 2);
    }

    public function get_items_history()
    {
        global $configs;
        // Fetch the items for the current cart from the cart_items table
        $cart_id = $this->get_id();  // Assuming `get_id()` returns the cart's ID
        $query = "SELECT item_id, quantity FROM $configs->DB_NAME.$configs->DB_CART_ITEMS_TABLE WHERE `cart_id` = $cart_id";

        // Execute the query to retrieve items and their quantities
        $result = run_select_query($query);

        $items = [];

        // If the query returns results, loop through and populate the items array
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Get item details (assuming there's a method to fetch item details by ID)
                $item = ShopItem::get_by_id($row['item_id']);
                if ($item) {
                    $items[] = [
                        'item' => $item,  // The actual item object
                        'quantity' => $row['quantity']  // The quantity for the item
                    ];
                }
            }
        }

        return $items;
    }

    static public function get_all_carts_by_user_id($user_id): array
    {
        global $configs;
        $carts = [];

        // Query to get all carts for the user
        $query = "SELECT * FROM $configs->DB_NAME.$configs->DB_CARTS_TABLE 
                  WHERE user_id = $user_id";

        $cartDataResult = run_select_query($query);

        if ($cartDataResult) {
            $cartDataArray = $cartDataResult->fetch_all(MYSQLI_ASSOC);
            $cartDataIterator = new itemIterator($cartDataArray);

            // Iterate over cart data using the CartIterator
            while ($cartDataIterator->hasNext()) {
                $cartData = $cartDataIterator->current();
                $cart = new Cart($cartData);

                // Fetch items for the cart using an iterator
                $itemsQuery = "SELECT * FROM $configs->DB_NAME.$configs->DB_CART_ITEMS_TABLE 
                               WHERE cart_id = {$cart->id}";
                $cartItemsResult = run_select_query($itemsQuery);

                if ($cartItemsResult) {
                    $cartItemsArray = $cartItemsResult->fetch_all(MYSQLI_ASSOC);
                    $cartItemsIterator = new itemIterator($cartItemsArray);

                    // Iterate over cart items using the CartIterator
                    while ($cartItemsIterator->hasNext()) {
                        $item = $cartItemsIterator->current();
                        $cart->items[$item['item_id']] = $item['quantity'];
                        $cartItemsIterator->next();
                    }
                }

                // Add the cart to the carts array
                $carts[] = $cart;
                $cartDataIterator->next();
            }
        }

        return $carts;
    }


    static public function get_completed_carts_by_user_id($user_id): array
    {
        global $configs;
        $carts = [];

        $query = "SELECT * FROM $configs->DB_NAME.$configs->DB_CARTS_TABLE 
                  WHERE user_id = $user_id AND status = 'completed'";

        $result = run_select_query($query);

        if ($result) {
            $cartDataArray = $result->fetch_all(MYSQLI_ASSOC);
            $cartDataIterator = new itemIterator($cartDataArray);

            while ($cartDataIterator->hasNext()) {
                $cartData = $cartDataIterator->current();
                $cart = new Cart($cartData);

                // Fetch items for the cart using an iterator
                $itemsQuery = "SELECT * FROM $configs->DB_NAME.$configs->DB_CART_ITEMS_TABLE 
                               WHERE cart_id = {$cart->id}";
                $cartItemsResult = run_select_query($itemsQuery);

                if ($cartItemsResult) {
                    $cartItemsArray = $cartItemsResult->fetch_all(MYSQLI_ASSOC);
                    $cartItemsIterator = new itemIterator($cartItemsArray);

                    while ($cartItemsIterator->hasNext()) {
                        $item = $cartItemsIterator->current();
                        $cart->items[$item['item_id']] = $item['quantity'];
                        $cartItemsIterator->next();
                    }
                }

                $carts[] = $cart;
                $cartDataIterator->next();
            }
        }

        return $carts;
    }


    // Get carts owned by a user via the user's ID
    static public function get_current_cart_by_user_id($user_id): ?Cart
    {
        global $configs;

        $query = "SELECT * FROM $configs->DB_NAME.$configs->DB_CARTS_TABLE 
                  WHERE user_id = $user_id AND status = 'current'";

        $result = run_select_query($query)->fetch_assoc();

        if ($result) {
            $cart = new Cart($result);

            // Fetch cart items
            $cart_items = run_select_query("
                SELECT * FROM $configs->DB_NAME.$configs->DB_CART_ITEMS_TABLE 
                WHERE cart_id = $cart->id
            ")->fetch_all(MYSQLI_ASSOC);

            // Create an iterator for the cart items
            $cartItemsIterator = new itemIterator($cart_items);

            // Use the iterator to add items to the cart
            while ($cartItemsIterator->hasNext()) {
                $item = $cartItemsIterator->current();
                $cart->items[$item['item_id']] = $item['quantity'];
                $cartItemsIterator->next(); // Move to the next item
            }

            return $cart;
        }

        return null; // No current cart
    }

    public static function checkout_cart(int $cart_id): bool
    {
        global $configs;

        // Update cart status to 'completed'
        $query = "UPDATE $configs->DB_NAME.$configs->DB_CARTS_TABLE 
              SET status = 'completed' 
              WHERE id = $cart_id";

        return run_query($query);
    }

    public static function add_new_cart(int $user_id): void
    {
        global $configs;
        run_query("INSERT INTO $configs->DB_NAME.$configs->DB_CARTS_TABLE (user_id) VALUES ($user_id)");
    }

    public static function create_new_cart(int $user_id): bool
    {
        global $configs;

        $query = "INSERT INTO $configs->DB_NAME.$configs->DB_CARTS_TABLE (user_id, status) 
                  VALUES ($user_id, 'current')";

        return run_query($query);
    }
    // Add a certain item to a certain user's cart
    public function add_item_to_cart($item_id): bool
    {
        global $configs;
        $cart_id = $this->get_id(); // Assuming `get_id()` returns the cart's ID
        // Check if item already exists
        if (run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_CART_ITEMS_TABLE WHERE `cart_id` = $cart_id AND `item_id` = $item_id")->num_rows > 0) {
            // Increment if exists
            return run_query("UPDATE $configs->DB_NAME.$configs->DB_CART_ITEMS_TABLE SET `quantity` = `quantity` + 1 WHERE `cart_id` = $cart_id AND `item_id` = $item_id");
        } else {
            // Otherwise add new entry
            return run_query("INSERT INTO $configs->DB_NAME.$configs->DB_CART_ITEMS_TABLE (`cart_id`, `item_id`, `quantity`) VALUES ($cart_id, $item_id, 1)");
        }
    }
    // Remove item from cart
    public function remove_item_from_cart($item_id): bool
    {
        global $configs;
        $cart_id = $this->get_id(); // Assuming `get_id()` returns the cart's ID
        // Check if item exists in the cart
        $result = run_select_query("SELECT quantity FROM $configs->DB_NAME.$configs->DB_CART_ITEMS_TABLE WHERE `cart_id` = cart_id AND `item_id` = $item_id");
        if ($result->num_rows > 0) {
            // Fetch the current quantity
            $currentQuantity = (int) $result->fetch_assoc()['quantity'];

            if ($currentQuantity > 1) {
                // Decrement the quantity if more than 1
                $success = run_query("UPDATE $configs->DB_NAME.$configs->DB_CART_ITEMS_TABLE SET `quantity` = `quantity` - 1 WHERE `cart_id` = $cart_id AND `item_id` = $item_id");
                if (!$success) {
                    error_log("Database update failed: " . mysqli_error($configs->DB_CONN)); // Log the error
                }
                return $success;
            } else {
                // Remove the item entirely if quantity is 1
                $success = run_query("DELETE FROM $configs->DB_NAME.$configs->DB_CART_ITEMS_TABLE WHERE `cart_id` = $cart_id AND `item_id` = $item_id");
                if (!$success) {
                    error_log("Database delete failed: " . mysqli_error($configs->DB_CONN)); // Log the error
                }
                return $success;
            }
        }
        return false; // Return false if item does not exist
    }
    // Delete all carts and their items owned by a specific user via the user's ID
    static public function delete_cart_by_user_id(int $user_id): bool
    {
        global $configs;
        $success = true;

        // Get all cart IDs associated with the user
        $cart_ids = run_select_query("SELECT id FROM $configs->DB_NAME.$configs->DB_CARTS_TABLE WHERE `user_id` = $user_id")->fetch_all(MYSQLI_ASSOC);
        // Use the CartIterator to iterate over cart IDs
        $cartIterator = new itemIterator($cart_ids);

        while ($cartIterator->hasNext()) {
            $cart = $cartIterator->current();
            $cart_id = $cart['id'];

            // Delete the cart itself
            if (!run_query("DELETE FROM $configs->DB_NAME.$configs->DB_CARTS_TABLE WHERE `id` = $cart_id")) {
                error_log("Failed to delete cart ID $cart_id: " . mysqli_error($configs->DB_CONN));
                $success = false;
            }

            $cartIterator->next(); // Move to the next cart
        }


        return $success;
    }
}
