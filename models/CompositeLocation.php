<?php 
class CompositeLocation implements LocationComponent {
    private $id;
    private $name;
    private $components = array();
    
    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }
    
    public function addComponent(LocationComponent $component) {
        $this->components[] = $component;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getDescription() {
        $fullDescription = $this->name;
        foreach ($this->components as $component) {
            $fullDescription = $component->getName() . ', ' . $fullDescription; // Append sublocation
        }
        return $fullDescription;
    }

    public function getComponents() {
        return $this->components;
    }
}
?>