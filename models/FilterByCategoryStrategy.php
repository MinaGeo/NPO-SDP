<?php
class FilterByCategoryStrategy implements IFilter {
    private string $category;

    public function __construct(string $category) {
        $this->category = $category;
    }

    public function filter(array $data): array {
        return array_filter($data, function($item) {
            return $item['category'] === $this->category;
        });
    }
}
?>