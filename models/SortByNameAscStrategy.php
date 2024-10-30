<?php
class SortByNameAscStrategy implements ISort {
    public function sort(array $data): array {
        usort($data, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        return $data;
    }
}
?>
