<?php

namespace Tests\Helpers\Classes;

class Dog implements AnimalInterface
{
    public function eat(string $food): string
    {
        return $food;
    }
}