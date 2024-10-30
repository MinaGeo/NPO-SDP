<?php
class SortByRatingDescStrategy implements ISort {
    public function sort(array $data): array {
        usort($data, function($a, $b) {
            return $b['rating'] <=> $a['rating'];
        });
        return $data;
    }
}
?>