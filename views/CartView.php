<?php
class CartView{
    public function showCart($cart_items, $cart){
        require_once './views/Navbar.php';
        require_once './views/pages/CartPage.php';
    }
}
?>