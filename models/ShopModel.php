<?php

declare(strict_types=1);

ob_start();
require_once "../db.php";
ob_end_clean();

class ShopItem
{
    // Define properties
    public int $id;
    public string $name;
    public string $description;
    public int $price;

    // Constructor that initializes properties with type casting
    private function __construct(array $properties)
    {
        $this->id = (int)($properties['id'] ?? 0); // Cast to int
        $this->name = $properties['name'] ?? '';
        $this->description = $properties['description'] ?? '';
        $this->price = (int)($properties['price'] ?? 0);
    }

    public function __toString(): string
    {
        $str = '<pre>';
        foreach ($this as $key => $value) {
            $str .= "$key: $value<br/>";
        }
        return $str . '</pre>';
    }

    static public function get_by_id(int $id): ?ShopItem
    {
        $rows = run_select_query("SELECT * FROM `NPO`.`shop_items` WHERE id = ?", [$id]);
        return $rows && $rows->num_rows > 0 ? new ShopItem($rows->fetch_assoc()) : null;
    }

    // Get every Shop Item
    static public function get_all(): array
    {
        $shop_items = [];
        $rows = run_select_query("SELECT * FROM `shop_items`")->fetch_all(MYSQLI_ASSOC);

        foreach ($rows as $row) {
            $shop_items[] = new ShopItem($row); // Corrected variable name
        }
        return $shop_items;
    }

    // Add an Shop Item
    static public function add_shop_item(string $name, string $description, int $price): bool
    {
        // Check if Shop Item already exists
        $checkShopItem = run_select_query("SELECT * FROM `NPO`.`shop_items` WHERE `name` = ?", [$name], true);
        
        // Use num_rows to check if there are no results
        if ($checkShopItem && $checkShopItem->num_rows == 0) {  
            // Insert the new Shop Item
            return run_query("INSERT INTO `NPO`.`shop_items` (`name`, `description`, `price`) VALUES (?, ?, ?)", 
                [$name, $description, $price], true);
        }
    
        return false; // Return false if Shop Item already exists
    }

    // Delete Shop Item
    static public function delete_shop_item(int $id): bool
    {
        // Check if Shop Item exists
        $result = run_select_query("SELECT * FROM `NPO`.`shop_items` WHERE `id` = $id");

        if ($result && $result->num_rows > 0) {
            // Remove the Shop Item
            $success = run_query("DELETE FROM `NPO`.`shop_items` WHERE `id` = $id");

            if (!$success) {
                error_log("Database delete failed..."); // Log the error
            }

            return $success;
        }

        return false; // Return false if shop item does not exist
    }
}
