<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "./models/ShopModel.php";
include "./models/CartModel.php";

class CartController
{
    public function showCart($userId)
    {
        // Get user data
        //$user = User::get_by_id($userId);

        // Fetch the user's cart (assuming the first cart is used)
        $cart = Cart::get_by_user_id($userId)[0];

        // Fetch each item in the cart along with its details
        $cart_items = [];
        foreach ($cart->items as $itemId => $quantity) {
            // Get details of each shirt using the item ID
            $itemDetails = ShopItem::get_by_id($itemId);
            if ($itemDetails) {
                $cart_items[] = [
                    'item' => $itemDetails,
                    'quantity' => $quantity
                ];
                //echo 'i am here';
            }
        }
        // Pass the items to the cart view
        require_once "./views/CartView.php";
    }

    public function removeCartItem(){
        if (isset($_POST['removeFromCart'])) {
            if (!empty($_POST['userId']) && !empty($_POST['itemId'])) {
                $cart = Cart::get_by_user_id($_POST['userId'])[0];
                $result = Cart::remove_item_from_cart($cart->id, $_POST['itemId']);
    
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

    public function checkout(){
        echo "I am here";
        if (isset($_POST['checkout'])) {
            if (!empty($_POST['userId'])) {

                $result = Cart::delete_cart_by_user_id($_POST['userId']);
    
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