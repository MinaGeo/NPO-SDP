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
        if (!in_array($component, $this->components, true)) { 
            $this->components[] = $component; 
            if ($addToDb) { 
                if ($component instanceof ShopItem) { 
                    run_query("INSERT INTO `category_items` (category_id, item_id) VALUES (?, ?)", [$this->id, $component->get_id()]); 
                } else if ($component instanceof ShopCategory) { 
                    run_query("INSERT INTO `category_items` (category_id, subcategory_id) VALUES (?, ?)", [$this->id, $component->get_id()]); 
                } 
            } 
        }
    }
    

    public function remove(IShopComponent $component): void { 
        $key = array_search($component, $this->components, true); 
        if ($key !== false) { 
            unset($this->components[$key]); 
            if ($component instanceof ShopItem) { 
                run_query("DELETE FROM `category_items` WHERE category_id = ? AND item_id = ?", [$this->id, $component->get_id()]); 
            } else if ($component instanceof ShopCategory) { 
                run_query("DELETE FROM `category_items` WHERE category_id = ? AND subcategory_id = ?", [$this->id, $component->get_id()]); 
            } 
        }
    }
    public function loadComponents(): void { 
        $component_rows = run_select_query("SELECT * FROM `category_items` WHERE `category_id` = ?", [$this->id])->fetch_all(MYSQLI_ASSOC); 
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

    public function __toString(): string { 
        $str = "<pre>ID: $this->id<br/>Category: $this->name<br/>Components:<br/>";
        foreach ($this->components as $component) { 
            $str .= $component->__toString(); 
        } 
        return $str . '</pre>'; 
    }

    
    static public function get_all(): array { 
        $categories = []; 
        $rows = run_select_query("SELECT * FROM `shop_categories`")->fetch_all(MYSQLI_ASSOC); 
        foreach ($rows as $row) { 
            $category = new ShopCategory($row['id'], $row['name']); 
            $category->loadComponents(); 
            // Load components without adding to DB 
            $categories[] = $category; 
        } return $categories; 
    }
    
    static public function get_by_name(string $name): ?ShopCategory {
        $rows = run_select_query("SELECT * FROM `shop_categories` WHERE `name` = ?", [$name]);
        return $rows && $rows->num_rows > 0 ? new ShopCategory($rows->fetch_assoc()['id'], $name) : null;
    }

    static public function get_by_id(int $id): ?ShopCategory {
        $rows = run_select_query("SELECT * FROM `shop_categories` WHERE `id` = ?", [$id]);
        return $rows && $rows->num_rows > 0 ? new ShopCategory($id, $rows->fetch_assoc()['name']) : null;
    }
}
?>