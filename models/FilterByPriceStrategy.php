<?php
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

?>