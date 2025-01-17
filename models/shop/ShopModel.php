<?php

declare(strict_types=1);

ob_start();
require_once "./db_setup.php";
ob_end_clean();
require_once "./models/cart/CartDecorater.php";
require_once "./models/itemIterator.php";
require_once "IShopComponent.php";
require_once "ShopCategory.php";
require_once "./models/IAggregater.php";
class ShopItem implements IShopComponent, IAggregater
{
    // Define properties
    private int $id;
    private string $name;
    private string $description;
    private float $price;

    public function get_id(): int
    {
        return $this->id;
    }
    public function get_name(): string
    {
        return $this->name;
    }
    public function get_description(): string
    {
        return $this->description;
    }
    public function get_price(): float
    {
        return $this->price;
    }

    // Constructor that initializes properties with type casting
    private function __construct(array $properties)
    {
        $this->id = (int)($properties['id'] ?? 0); // Cast to int
        $this->name = $properties['name'] ?? '';
        $this->description = $properties['description'] ?? '';
        $this->price = (float)($properties['price'] ?? 0);
    }

    public function getIterator($items = ''): itemIterator
    {
        return new itemIterator([
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price
        ]);
    }

    public function __toString(): string
    {
        $str = '<pre>';
        $iterator = $this->getIterator(); // Assuming getIterator() returns a ShopIterator
        while ($iterator->hasNext()) {
            $key = $iterator->currentKey();
            $value = $iterator->current();
            $str .= "$key: $value<br/>";
            $iterator->next();
        }
        return $str . '</pre>';
    }

    static public function get_by_id(int $id): ?ShopItem
    {
        global $conn, $configs;
        $rows = $conn->run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_SHOP_ITEMS_TABLE WHERE id = ?", [$id]);
        return $rows && $rows->num_rows > 0 ? new ShopItem($rows->fetch_assoc()) : null;
    }

    // Get every Shop Item
    static public function get_all(): array
    {
        global $conn, $configs;
        $shop_items = [];
        $rows = $conn->run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_SHOP_ITEMS_TABLE")->fetch_all(MYSQLI_ASSOC);

        $shopIterator = new itemIterator($rows);
        while ($shopIterator->hasNext()) {
            $shop_items[] = new ShopItem($shopIterator->next());
        }
        return $shop_items;
    }

    // Add an Shop Item
    static public function add_shop_item(string $name, string $description, float $price): bool
    {
        global $conn, $configs;
        // Check if Shop Item already exists
        $checkShopItem = $conn->run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_SHOP_ITEMS_TABLE WHERE `name` = ?", [$name], true);

        // Use num_rows to check if there are no results
        if ($checkShopItem && $checkShopItem->num_rows == 0) {
            // Insert the new Shop Item
            return $conn->run_query(
                "INSERT INTO $configs->DB_NAME.$configs->DB_SHOP_ITEMS_TABLE (`name`, `description`, `price`) VALUES (?, ?, ?)",
                [$name, $description, $price],
                true
            );
        }

        return false; // Return false if Shop Item already exists
    }

    
    public static function get_by_name(string $name): ?ShopItem
    {
        global $conn, $configs;
        $rows = $conn->run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_SHOP_ITEMS_TABLE WHERE name = ?", [$name]);
        return $rows && $rows->num_rows > 0 ? new ShopItem($rows->fetch_assoc()) : null;
    }
    
    public static function delete_shop_item(int $id): bool
    {
        global $conn, $configs;
    
        // Check if the Shop Item exists
        $result = $conn->run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_SHOP_ITEMS_TABLE WHERE `id` = ?", [$id]);
    
        if ($result && $result->num_rows > 0) {
            // Try to remove category associations regardless of whether they exist
            $delete_category_associations = $conn->run_query("DELETE FROM $configs->DB_NAME.$configs->DB_CATEGORY_ITEMS_TABLE WHERE item_id = ?", [$id]);
            if ($delete_category_associations === false) {
                error_log("Failed to delete category associations for item_id: $id");
                // Continue even if no associations exist
            }
    
            // Remove the Shop Item
            $delete_item = $conn->run_query("DELETE FROM $configs->DB_NAME.$configs->DB_SHOP_ITEMS_TABLE WHERE `id` = ?", [$id]);
            if (!$delete_item) {
                error_log("Failed to delete shop item with id: $id");
                return false; // Stop if the item cannot be deleted
            }
    
            return true; // Item deleted successfully
        }
    
        // Log if the shop item does not exist
        error_log("Shop item with id: $id does not exist");
        return false;
    }
    




    public function add(IShopComponent $component): void
    {
        // Not implemented in leaf node
    }

    public function remove(IShopComponent $component): void
    {
        // Not implemented in leaf node
    }

    public function update(array $properties): bool
    {
        $this->name = $properties['name'] ?? $this->name;
        $this->description = $properties['description'] ?? $this->description;
        $this->price = (float)($properties['price'] ?? $this->price);
        return false;
    }

    public function get_category(): ?ShopCategory
    {
        global $conn, $configs;
        $row = $conn->run_select_query("SELECT category_id FROM $configs->DB_NAME.$configs->DB_CATEGORY_ITEMS_TABLE WHERE item_id = ?", [$this->id])->fetch_assoc();
        return $row ? ShopCategory::get_by_id((int)$row['category_id']) : null;
    }
}
