<?php

namespace Classes;

class Dog implements AnimalInterface
{
    public function eat(mixed $food): string
    {
        return $food;
    }
}