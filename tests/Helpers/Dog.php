<?php

namespace Tests\Helpers;

class Dog implements AnimalInterface
{
    public function eat(string $food): string
    {
        return $food;
    }
}