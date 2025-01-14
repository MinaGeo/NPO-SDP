<?php
require_once 'ICommand.php';
class RemoveItemFromCartCommand implements ICommand {
    private $cart;
    private $itemId;

    public function __construct($cart, $itemId) {
        $this->cart = $cart;
        $this->itemId = $itemId;
    }

    public function execute() {
        return $this->cart->remove_item_from_cart($this->itemId);
    }
}
?>