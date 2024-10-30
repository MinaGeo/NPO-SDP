<?php
class SortByPriceAscStrategy implements ISort {
    public function sort(array $data): array {
        usort($data, function($a, $b) {
            return $a['price'] <=> $b['price'];
        });
        return $data;
    }
}
?>