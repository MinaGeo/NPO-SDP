<?php

declare(strict_types=1);

ob_start();
require_once "./db_setup.php";
ob_end_clean();
// require_once "./models/ShopItem.php";
require_once "./models/CartDecorater.php";
class Cart
{
    // Define properties
    public int $id;
    public int $user_id;

    public $items = [];

    // Constructor that initializes properties with type casting
    private function __construct(array $properties)
    {
        $this->id = (int)($properties['id']); // Cast to int
        $this->user_id = (int)($properties['user_id']); // Cast to int
    }

    public function __toString(): string
    {
        $str = '<br/>';
        foreach ($this->items as $key => $value) {
            $str .= "    Item ID #$key: Qty $value<br/>";
        }
        return $str;
    }
    public function get_total_cart_price(): float
    {
        $price = 0;
        foreach ($this->items as $item_id => $quantity) {
            $shopItem = ShopItem::get_by_id($item_id); //model byklm model

            $price += $quantity * $shopItem->price;
        }
        return $price;
    }
    public function get_total_price_after_decoration(): float
    {
        $decoratedPrice = 0;
        foreach ($this->items as $item_id => $quantity) {
            $shopItem = ShopItem::get_by_id($item_id); //model byklm model
            $decoratedPrice += $quantity * (new ShippingDecorator(new VATDecorator($shopItem)))->calc_price();
        }
        return $decoratedPrice;
    }
    // Get a cart via its ID
    static public function get_by_id($id): Cart
    {
        global $configs;
        $cart = new Cart(run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_CARTS_TABLE WHERE `user_id` = $id")->fetch_assoc());
        //breturn el cart ely 3ayezha bel id
        $cart_items = run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_CART_ITEMS_TABLE WHERE `cart_id` = $cart->id")->fetch_all(MYSQLI_ASSOC);
        //breturn el items id using cart_item class
        foreach ($cart_items as $item) {
            $cart->items[$item['item_id']] = $item['quantity'];
        }
        return $cart;
    }

    // Get carts owned by a user via the user's ID
    static public function get_by_user_id($user_id): array
    {
        //EL USER MOMKEN YB2a 3ndo several carts.
        global $configs;
        $carts = [];
        foreach (run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_CARTS_TABLE WHERE `user_id` = $user_id")->fetch_all(MYSQLI_ASSOC) as $cart) {
            $carts[] = new Cart($cart);
        }
        foreach ($carts as $cart) {
            $cart_items = run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_CART_ITEMS_TABLE WHERE `cart_id` = $cart->id")->fetch_all(MYSQLI_ASSOC);
            foreach ($cart_items as $item) {
                $cart->items[$item['item_id']] = $item['quantity'];
            }
        }
        return $carts;
    }

    // Add a certain item to a certain user's cart
    static public function add_item_to_cart($cart_id, $item_id): bool
    {
        global $configs;
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
    static public function remove_item_from_cart($cart_id, $item_id): bool
    {
        global $configs;

        // Check if item exists in the cart
        $result = run_select_query("SELECT quantity FROM $configs->DB_NAME.$configs->DB_CART_ITEMS_TABLE WHERE `cart_id` = $cart_id AND `item_id` = $item_id");
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

        foreach ($cart_ids as $cart) {
            $cart_id = $cart['id'];
            // // Delete items in each cart
            // if (!run_query("DELETE FROM $configs->DB_NAME.$configs->DB_CART_ITEMS_TABLE WHERE `cart_id` = $cart_id")) {
            //     error_log("Failed to delete items for cart ID $cart_id: " . mysqli_error($configs->DB_CONN));
            //     $success = false;
            // }
            // Delete the cart itself
            if (!run_query("DELETE FROM $configs->DB_NAME.$configs->DB_CARTS_TABLE WHERE `id` = $cart_id")) {
                error_log("Failed to delete cart ID $cart_id: " . mysqli_error($configs->DB_CONN));
                $success = false;
            }
        }

        return $success;
    }
}
