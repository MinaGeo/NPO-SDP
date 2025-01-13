<?php
class FilteringContext {
    private IFilter $strategy;

    public function setStrategy(IFilter $strategy): void {
        $this->strategy = $strategy;
    }

    public function filterData(array $data): array {
        return $this->strategy->filter($data);
    }
}
?>