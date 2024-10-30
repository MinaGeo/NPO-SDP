<?php
class SortByPriceDescStrategy implements ISort {
    public function sort(array $data): array {
        usort($data, function($a, $b) {
            return $b['price'] <=> $a['price'];
        });
        return $data;
    }
}
?>