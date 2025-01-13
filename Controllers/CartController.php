<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "./models/shop/ShopModel.php";
require_once "./models/shop/CartModel.php";
require_once "./views/CartView.php";
require_once "./models/PaymentClasses.php";
class CartController implements IControl
{
    private $cartView;

    public function __construct()
    {
        $this->cartView = new cartView();
    }
    public function show()
    {
        // Fetch the user's current cart
        $cart = Cart::get_current_cart_by_user_id($_SESSION['USER_ID']);

        if (!$cart) {
            // If no current cart exists, create a new one
            Cart::create_new_cart($_SESSION['USER_ID']);
            $cart = Cart::get_current_cart_by_user_id($_SESSION['USER_ID']);
        }

        // Fetch each item in the cart along with its details
        $cart_items = [];
        $itemIterator = new itemIterator($cart->get_items());
        while ($itemIterator->hasNext()) {
            $itemId = $itemIterator->currentKey();
            $quantity = $itemIterator->current();
            $itemDetails = ShopItem::get_by_id($itemId);
            if ($itemDetails) {
                $cart_items[] = [
                    'item' => $itemDetails,
                    'quantity' => $quantity
                ];
            }
            $itemIterator->next();
        }
        $this->cartView->showCart($cart_items, $cart);
    }

    public function showCartHistory()
    {
        $user_id = $_SESSION['USER_ID'];
        $carts = Cart::get_completed_carts_by_user_id($user_id);
        $cart_history = [];  // Array to store cart history information

        $cartsIterator = new itemIterator($carts);
        while ($cartsIterator->hasNext()) {
            $cart = $cartsIterator->current();
            $cart_items = [];
            $itemIterator = new itemIterator($cart->get_items());
            while ($itemIterator->hasNext()) {
                $itemId = $itemIterator->currentKey();
                $quantity = $itemIterator->current();
                $itemDetails = ShopItem::get_by_id($itemId);
                if ($itemDetails) {
                    $cart_items[] = [
                        'name' => $itemDetails->get_name(),
                        'price' => $itemDetails->get_price(),
                        'quantity' => $quantity
                    ];
                }
                $itemIterator->next();
            }
            // Add cart details to cart history array
            $cart_history[] = [
                'cart' => $cart,
                'items' => $cart_items,
                'total_price' => $cart->get_total_cart_price(),
                'total_price_after_decoration' => $cart->get_total_price_after_decoration()
            ];
            $cartsIterator->next();
        }
        $this->cartView->showCartHistory($cart_history);
    }


    public function removeCartItem()
    {
        if (isset($_POST['removeFromCart'])) {
            if (!empty($_SESSION['USER_ID']) && !empty($_POST['itemId'])) {
                $cart = Cart::get_current_cart_by_user_id($_SESSION['USER_ID']);
                if ($cart) {
                    $result = Cart::remove_item_from_cart($cart->get_id(), $_POST['itemId']);
                    if ($result) {
                        echo json_encode(['success' => true, 'message' => 'Item removed from cart!']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to remove item from cart.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'No active cart found.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid input.']);
            }
            exit; // Ensure no further output is sent
        }
    }




    public function checkout()
    {
        if (isset($_POST['checkoutFlag'])) {
            if (!empty($_SESSION['USER_ID'])) {
                $paymentMethod = $_POST['paymentMethod'];
                $paymentContext = null;

                // Handle payment method details
                if ($paymentMethod === 'paypal') {
                    $paypalEmail = $_POST['paypalEmail'];
                    $paypalPassword = $_POST['paypalPassword'];
                    if (empty($paypalEmail) || empty($paypalPassword)) {
                        echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
                        exit;
                    }
                    $paymentContext = new PaymentContext(new PayByPaypal($paypalEmail, $paypalPassword));
                } elseif ($paymentMethod === 'creditCard') {
                    $cardNumber = $_POST['cardNumber'];
                    $cvv = $_POST['cvv'];
                    $expiryDate = $_POST['expiryDate'];
                    if (empty($cardNumber) || empty($cvv) || empty($expiryDate)) {
                        echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
                        exit;
                    }
                    $paymentContext = new PaymentContext(new PayByCreditCard($cardNumber, $cvv, $expiryDate));
                }

                if ($paymentContext) {
                    $totalPrice = $_POST['totalPrice'];
                    $paymentSuccess = $paymentContext->doPayment($totalPrice);
                    if ($paymentSuccess) {
                        // Mark the current cart as completed
                        $cart = Cart::get_current_cart_by_user_id($_SESSION['USER_ID']);
                        if ($cart) {
                            Cart::checkout_cart($cart->get_id()); // Update cart status to 'completed'
                            // Cart::create_new_cart($_SESSION['USER_ID']); // Create a new current cart
                            echo json_encode(['success' => true, 'message' => 'Checkout successful!']);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'No active cart found.']);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Payment failed.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid payment method.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'User not logged in.']);
            }
            exit;
        }
    }
}
