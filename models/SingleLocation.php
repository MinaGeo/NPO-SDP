<?php

class SingleLocation implements LocationComponent {
    private $id;
    private $name;
    
    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }
    
    public function getName() {
        return $this->name;
    }

    public function addComponent(LocationComponent $component) {
        // Leaf can't add components
    }

    public function getComponents() {
        return [];
    }
}

?>