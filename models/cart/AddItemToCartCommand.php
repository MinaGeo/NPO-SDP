<?php
require_once 'ICommand.php';
class AddItemToCartCommand implements ICommand {
    //private $cart;
    private $cartId;
    private $itemId;

    public function __construct($cartId, $itemId) {
        //$this->cart = $cart;
        $this->cartId = $cartId;
        $this->itemId = $itemId;
    }

    public function execute() {
        //return $this->cart->add_item_to_cart($this->itemId);
        return Cart::add_item_to_cart($this->cartId, $this->itemId);
    }
}
?>