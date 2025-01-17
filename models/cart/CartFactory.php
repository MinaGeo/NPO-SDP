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
    private static function getInvoker($cart, $itemId)
    {
        if (self::$invoker === null) {
            self::$invoker = new CartInvoker();
            $addItemCommand = new AddItemToCartCommand($cart, $itemId);
            $removeItemCommand = new RemoveItemFromCartCommand($cart, $itemId);
            self::$invoker->setOffCommand($removeItemCommand);
            self::$invoker->setOnCommand($addItemCommand);
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
        $invoker = self::getInvoker($cart, $itemId);
        // Process the action
        switch ($action) {
            case 'add':
                return $invoker->on();
    
            case 'remove':
                return $invoker->off();
    
            default:
                // Handle invalid action
                throw new InvalidArgumentException("Invalid action: $action");
        }
    }
    
}
