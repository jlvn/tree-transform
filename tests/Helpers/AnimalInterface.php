<?php

namespace Tests\Helpers;

interface AnimalInterface {
    public function eat(string $food): string;
}