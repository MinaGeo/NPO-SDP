<?php

// Untouched

interface Billable
{
    public function calc_price(): float;
}

abstract class ItemDecorator implements Billable
{
    protected Billable $billableItem;

    public function __construct(Billable $billableItem)
    {
        $this->billableItem = $billableItem;
    }

    abstract public function calc_price(): float;
}

class ShippingDecorator extends ItemDecorator
{
    public function calc_price(): float
    {
        return $this->billableItem->calc_price() * 1.05; //%5 
    }
}

class VATDecorator extends ItemDecorator
{
    public function calc_price(): float
    {
        return $this->billableItem->calc_price() * 1.14; 
    }
}
