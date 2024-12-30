<?php
class CartFactory
{
    // Method to create a new Cart object
    public static function createCart(int $user_id): Cart
    {
        // Check if a current cart exists for the user, if not, create a new one
        if (Cart::cart_exists_for_user($user_id)) {
            // Fetch the existing cart
            return Cart::get_current_cart_by_user_id($user_id);
        }

        // Otherwise, create a new cart for the user
        Cart::create_new_cart($user_id);
        return Cart::get_current_cart_by_user_id($user_id);
    }

    // Method to create a completed Cart, could be extended to handle other statuses
    public static function createCompletedCart(int $user_id): Cart
    {
        $completedCart = Cart::get_completed_carts_by_user_id($user_id);

        // Assuming the latest completed cart is the one needed, return the first one
        return $completedCart[0] ?? null; // Returns null if no completed cart exists
    }

    // You can add more methods for creating different types of carts with different statuses or conditions.
}

?>