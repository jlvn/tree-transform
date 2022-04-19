<?php

namespace Tests\Helpers\Classes;

interface AnimalInterface {
    public function eat(string $food): string;
}