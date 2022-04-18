<?php

use Jlvn\TreeTransform\Map;
use Jlvn\TreeTransform\NotFoundException;
use Jlvn\TreeTransform\NotFoundExceptionInterface;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @covers \Jlvn\TreeTransform\Map
 */
class MapTest extends TestCase
{
    /**
     * @test
     * @throws NotFoundExceptionInterface
     */
    public function it_can_set_a_key_to_a_value(): void {
        $map = new Map;
        $map->set('key', 'value');
        $this->assertEquals('value', $map->tryGet('key'));
    }

    /**
     * @test
     * @throws NotFoundExceptionInterface
     */
    public function it_throws_a_not_found_exception_when_getting_a_key_that_does_not_exist(): void {
        $this->expectException(NotFoundException::class);

        $map = new Map;

        $map->tryGet('key');
    }

    /** @test */
    public function it_can_return_a_default_value_when_a_key_does_not_exist(): void {
        $map = new Map;

        $this->assertEquals(null, $map->getOrDefault('key'));
        $this->assertEquals('default', $map->getOrDefault('key', 'default'));
    }
}