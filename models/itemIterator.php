<?php
require_once 'IIterator.php';
class itemIterator implements IIterator
{
    private array $items;
    private int $currentIndex = 0;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function hasNext(): bool
    {
        return $this->currentIndex < count($this->items);
    }

    public function next()
    {
        if ($this->hasNext()) {
            return $this->items[array_keys($this->items)[$this->currentIndex++]];
        }
        return null;
    }

    public function current()
    {
        if ($this->hasNext()) {
            return $this->items[array_keys($this->items)[$this->currentIndex]];
        }
        return null;
    }

    public function currentKey()
    {
        if ($this->hasNext()) {
            return array_keys($this->items)[$this->currentIndex];
        }
        return null;
    }

    public function remove(): void
    {
        if (isset(array_keys($this->items)[$this->currentIndex])) {
            unset($this->items[array_keys($this->items)[$this->currentIndex]]);
        }
    }
}
