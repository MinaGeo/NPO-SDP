<?php
require_once 'IIterator.php';
class PropertyIterator implements IIterator
{
    private array $properties; // Array of key-value pairs
    private int $currentIndex = 0; // Index of the current property

    public function __construct(array $properties)
    {
        $this->properties = array_map(
            fn($key, $value) => ['key' => $key, 'value' => $value],
            array_keys($properties),
            array_values($properties)
        );
    }

    public function hasNext(): bool
    {
        return $this->currentIndex < count($this->properties);
    }

    public function next()
    {
        if ($this->hasNext()) {
            return $this->properties[$this->currentIndex++];
        }

        return null;
    }

    public function current()
    {
        return $this->properties[$this->currentIndex] ?? null;
    }

    public function currentKey()
    {
        return $this->current()['key'] ?? null;
    }

    public function remove(): void
    {
        if ($this->currentIndex < count($this->properties)) {
            array_splice($this->properties, $this->currentIndex, 1);
        }
    }
}
