<?php
require_once './models/cart/CartModel.php';
require_once './models/cart/CartInvoker.php';
require_once './models/cart/AddItemToCartCommand.php';
require_once './models/cart/RemoveItemFromCartCommand.php';

class CartFactory
{
    // Shared instance of the invoker
    private static $invoker = null;

    // Get or create the shared CartInvoker
    private static function getInvoker()
    {
        if (self::$invoker === null) {
            self::$invoker = new CartInvoker();
        }
        return self::$invoker;
    }

    public static function getCartForUser($userId)
    {
        // Retrieve the current cart for the user
        $cart = Cart::get_current_cart_by_user_id($userId);

        // If no cart exists, create a new one
        if (!$cart) {
            Cart::create_new_cart($userId);
            $cart = Cart::get_current_cart_by_user_id($userId);
        }

        return $cart;
    }

    public static function cartOptions(string $action, $userId, $itemId)
    {
        // Retrieve the user's cart
        $cart = self::getCartForUser($userId);
    
        // Process the action
        switch ($action) {
            case 'add':
                // Create the add item command
                $addItemCommand = new AddItemToCartCommand($cart, $itemId);
                $invoker = self::getInvoker();
                $invoker->setOnCommand($addItemCommand);
                return $invoker->on();
    
            case 'remove':
                // Create the remove item command
                $removeItemCommand = new RemoveItemFromCartCommand($cart, $itemId);
                $invoker = self::getInvoker();
                $invoker->setOffCommand($removeItemCommand);
                return $invoker->off();
    
            default:
                // Handle invalid action
                throw new InvalidArgumentException("Invalid action: $action");
        }
    }
    

    public static function addItemToCart($userId, $itemId)
    {
        // Retrieve the user's cart
        $cart = self::getCartForUser($userId);

        // Set up the add item command
        $addItemCommand = new AddItemToCartCommand($cart, $itemId);

        // Use the shared invoker
        $invoker = self::getInvoker();
        $invoker->setOnCommand($addItemCommand);

        // Execute the "on" method to add the item
        return $invoker->on();
    }

    public static function removeItemFromCart($userId, $itemId)
    {
        // Retrieve the user's cart
        $cart = self::getCartForUser($userId);

        // If no cart exists, return false
        if (!$cart) {
            return false;
        }

        // Set up the remove item command
        $removeItemCommand = new RemoveItemFromCartCommand($cart, $itemId);

        // Use the shared invoker
        $invoker = self::getInvoker();
        $invoker->setOffCommand($removeItemCommand);

        // Execute the "off" method to remove the item
        return $invoker->off();
    }
}
