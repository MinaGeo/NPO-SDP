<?php
class FilterByRatingStrategy implements IFilter {
    private float $minRating;

    public function __construct(float $minRating) {
        $this->minRating = $minRating;
    }

    public function filter(array $data): array {
        return array_filter($data, function($item) {
            return $item['rating'] >= $this->minRating;
        });
    }
}
?>