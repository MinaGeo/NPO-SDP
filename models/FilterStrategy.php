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
class FilterByEventTypeStrategy implements IFilter {
    private string $eventType;

    public function __construct(string $eventType = '') {
        $this->eventType = $eventType;
    }

    public function filter(array $data): array {
        if (empty($this->eventType)) {
            return $data; // Return all if no event type is set
        }
        return array_filter($data, function($event) {
            return $event->type === $this->eventType; // Filter events by type
        });
    }
}

class FilterByPriceStrategy implements IFilter {
    private float $minPrice;
    private float $maxPrice;

    public function __construct(float $minPrice, float $maxPrice) {
        $this->minPrice = $minPrice;
        $this->maxPrice = $maxPrice;
    }

    public function filter(array $data): array {
        $minPrice = $this->minPrice; //To be defined in the scope
        $maxPrice = $this->maxPrice;
        return array_filter($data, function($item) use ($minPrice, $maxPrice) { //using the "use" keyword, the anonymous function can access $minPrice and $maxPrice
            return $item['price'] >= $minPrice && $item['price'] <= $maxPrice;
        });
    }
}

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