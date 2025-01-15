<?php

// Define the ShopComponent interface 
interface IShopComponent { 
    public function get_id(): int; 
    public function get_name(): string; 
    public function get_description(): string; 
    public function get_price(): float; 
    public function add(IShopComponent $component): void; 
    public function remove(IShopComponent $component): void; 
    public function update(array $properties): bool; 
}
?>