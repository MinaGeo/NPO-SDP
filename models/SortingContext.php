<?php class SortingContext {
    private ISort $strategy;

    public function setStrategy(ISort $strategy): void {
        $this->strategy = $strategy;
    }

    public function sortData(array $data): array {
        return $this->strategy->sort($data);
    }
}
?>