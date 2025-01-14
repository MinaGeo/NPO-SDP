<?php
require_once 'ICommand.php';
class AddItemToCartCommand implements ICommand {
    private $cart;
    private $itemId;

    public function __construct($cart, $itemId) {
        $this->cart = $cart;
        $this->itemId = $itemId;
    }

    public function execute() {
        
        return $this->cart->add_item_to_cart($this->itemId);
    }
}
?>