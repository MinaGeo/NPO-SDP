<?php
require_once './models/cart/CartModel.php';
require_once './models/cart/CartInvoker.php';
require_once './models/cart/AddItemToCartCommand.php';
require_once './models/cart/RemoveItemFromCartCommand.php';

class CartFactory
{
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

    public static function addItemToCart($userId, $itemId)
    {
        // Retrieve the user's cart
        $cart = Cart::get_current_cart_by_user_id($userId);

        // If no cart exists, create a new one
        if (!$cart) {
            Cart::create_new_cart($userId);
            $cart = Cart::get_current_cart_by_user_id($userId);
        }

        // Set up the invoker and add item command
        $invoker = new CartInvoker();
        $addItemCommand = new AddItemToCartCommand($cart, $itemId);
        $invoker->setOnCommand($addItemCommand);

        // Execute the "on" method to add the item
        return $invoker->on();
    }

    public static function removeItemFromCart($userId, $itemId)
    {
        // Retrieve the user's cart
        $cart = Cart::get_current_cart_by_user_id($userId);

        if (!$cart) {
            return false; // No cart to remove items from
        }

        // Set up the invoker and remove item command
        $invoker = new CartInvoker();
        $removeItemCommand = new RemoveItemFromCartCommand($cart, $itemId);
        $invoker->setOffCommand($removeItemCommand);

        // Execute the "off" method to remove the item
        return $invoker->off();
    }
}
