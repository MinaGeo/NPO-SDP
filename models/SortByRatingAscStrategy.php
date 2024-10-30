<?php
class SortByRatingAscStrategy implements ISort {
    public function sort(array $data): array {
        usort($data, function($a, $b) {
            return $a['rating'] <=> $b['rating'];
        });
        return $data;
    }
}
?>
