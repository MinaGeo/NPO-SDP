<?php

// Untouched

interface Billable
{
    public function calc_price(): float;
}

abstract class CartDecorator implements Billable
{
    protected Billable $billableItem;

    public function __construct(Billable $billableItem)
    {
        $this->billableItem = $billableItem;
    }

    abstract public function calc_price(): float;
}

class ShippingDecorator extends CartDecorator
{
    public function calc_price(): float
    {
        return $this->billableItem->calc_price() * 1.05; //%5 
    }
}

class VATDecorator extends CartDecorator
{
    public function calc_price(): float
    {
        return $this->billableItem->calc_price() * 1.14; 
    }
}
