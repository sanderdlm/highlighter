<?php

// Great source of vitamins
class Vegetable
{
    public readonly string $edible;
    public readonly string $color;

    public function __construct(string $edible, string $color = "green")
    {
        $this->edible = $edible;
        $this->color = $color;
    }

    public function isEdible(): string
    {
        return $this->edible;
    }

    public function getColor(): string
    {
        return $this->color;
    }
}