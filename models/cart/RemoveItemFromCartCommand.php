<?php
require_once 'ICommand.php';
class RemoveItemFromCartCommand implements ICommand {
    //private $cart;
    private $cartId;
    private $itemId;

    public function __construct($cartId, $itemId) {
        //$this->cart = $cart;
        $this->cartId = $cartId;
        $this->itemId = $itemId;
    }

    public function execute() {
        //return $this->cart->remove_item_from_cart($this->itemId);
        return Cart::remove_item_from_cart($this->cartId, $this->itemId);
    }
}
?>