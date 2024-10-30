<?php
class SortByNameDescStrategy implements ISort {
    public function sort(array $data): array {
        usort($data, function($a, $b) {
            return strcmp($b['name'], $a['name']);
        });
        return $data;
    }
}
?>