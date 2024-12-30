<?php

interface LocationComponent {
    public function getName();
    public function addComponent(LocationComponent $component);
    public function getComponents();
}

