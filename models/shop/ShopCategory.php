<?php
class ShopCategory implements IShopComponent {
    private int $id; 
    private string $name; 
    private array $components = []; 
    
    public function __construct(int $id, string $name) { 
        $this->id = $id; 
        $this->name = $name; 
    }
    public function add(IShopComponent $component, bool $addToDb = true): void {
    // Check if the component already exists to avoid duplication 
    global $conn, $configs;
        if (!in_array($component, $this->components, true)) { 
            $this->components[] = $component; 
            if ($addToDb) { 
                if ($component instanceof ShopItem) { 
                    $conn->run_query("INSERT INTO $configs->DB_NAME.$configs->DB_CATEGORY_ITEMS_TABLE (category_id, item_id) VALUES (?, ?)", [$this->id, $component->get_id()]); 
                } else if ($component instanceof ShopCategory) { 
                    $conn->run_query("INSERT INTO $configs->DB_NAME.$configs->DB_CATEGORY_ITEMS_TABLE (category_id, subcategory_id) VALUES (?, ?)", [$this->id, $component->get_id()]); 
                } 
            } 
        }
    }
    

    public function remove(IShopComponent $component): void { 
        global $conn, $configs;
        $key = array_search($component, $this->components, true); 
        if ($key !== false) { 
            unset($this->components[$key]); 
            if ($component instanceof ShopItem) { 
                $conn->run_query("DELETE FROM $configs->DB_NAME.$configs->DB_CATEGORY_ITEMS_TABLE WHERE category_id = ? AND item_id = ?", [$this->id, $component->get_id()]); 
            } else if ($component instanceof ShopCategory) { 
                $conn->run_query("DELETE FROM $configs->DB_NAME.$configs->DB_CATEGORY_ITEMS_TABLE WHERE category_id = ? AND subcategory_id = ?", [$this->id, $component->get_id()]); 
            } 
        }
    }
    public function loadComponents(): void { 
        global $conn, $configs;
        $component_rows = $conn->run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_CATEGORY_ITEMS_TABLE WHERE `category_id` = ?", [$this->id])->fetch_all(MYSQLI_ASSOC); 
        foreach ($component_rows as $component_row) { 
            if (isset($component_row['item_id'])) { 
                $this->components[] = ShopItem::get_by_id((int)$component_row['item_id']); 
            } else if (isset($component_row['subcategory_id'])) { 
                $this->components[] = ShopCategory::get_by_id((int)$component_row['subcategory_id']); 
            } 
        } 
    }
    public function getComponents(): array { 
        return $this->components; 
    }    
    public function update(array $properties): bool { 
        $this->name = $properties['name'] ?? $this->name; 
        return true; 
    }

    public function get_id(): int { 
        return $this->id; 
    }

    public function get_name(): string { 
        return $this->name; 
    }

    public function get_description(): string { 
        return ''; 
    }

    public function get_price(): float { 
        return array_reduce($this->components, function ($total, $component) {
            return $total + $component->get_price(); 
        }, 0.0); 
    }
    
    static public function get_all(): array { 
        $categories = []; 
        global $conn, $configs;
        $rows = $conn->run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_SHOP_CATEGORIES_TABLE ")->fetch_all(MYSQLI_ASSOC); 
        foreach ($rows as $row) { 
            $category = new ShopCategory($row['id'], $row['name']); 
            $category->loadComponents(); 
            // Load components without adding to DB 
            $categories[] = $category; 
        } return $categories; 
    }
    public static function add_category(string $categoryName, string $description = ''): bool
    {
        global $conn, $configs;
        $rows = $conn->run_query("INSERT INTO $configs->DB_NAME.$configs->DB_SHOP_CATEGORIES_TABLE (`name`, `description`) VALUES (?, ?)", [$categoryName, $description]);
        return $rows;
    }
    
    static public function get_by_name(string $name): ?ShopCategory {
        global $conn, $configs;
        $rows = $conn->run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_SHOP_CATEGORIES_TABLE WHERE `name` = ?", [$name]);
        return $rows && $rows->num_rows > 0 ? new ShopCategory($rows->fetch_assoc()['id'], $name) : null;
    }

    static public function get_by_id(int $id): ?ShopCategory {
        global $conn, $configs;
        $rows = $conn->run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_SHOP_CATEGORIES_TABLE WHERE `id` = ?", [$id]);
        return $rows && $rows->num_rows > 0 ? new ShopCategory($id, $rows->fetch_assoc()['name']) : null;
    }
}
?>