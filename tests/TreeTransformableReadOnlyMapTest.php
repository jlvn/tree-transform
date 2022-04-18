<?php

use Classes\Dog;
use Jlvn\TreeTransform\GenericTreeTransformable;
use Jlvn\TreeTransform\Map;
use Jlvn\TreeTransform\NotFoundException;
use Jlvn\TreeTransform\TreeTransformableTagReadOnlyMap;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @covers \Jlvn\TreeTransform\TreeTransformableTagReadOnlyMap
 */
class TreeTransformableReadOnlyMapTest extends TestCase
{
    /** @test
     * @throws NotFoundException
     */
    public function it_converts_a_tree_transformable_array_to_map(): void {
        $transformable = new GenericTreeTransformable(Dog::class, fn() => [], fn() => []);

        $map = new TreeTransformableTagReadOnlyMap([
            $transformable
        ]);

        $this->assertEquals($transformable, $map->tryGet(Dog::class));
    }

    /** @test
     * @throws NotFoundException
     */
    public function it_keeps_the_last_transformable_of_a_type_when_converting_a_array(): void {
        $first = new GenericTreeTransformable(Dog::class, fn() => [], fn() => 'first');
        $last = new GenericTreeTransformable(Dog::class, fn() => [], fn() => 'last');

        $map = new TreeTransformableTagReadOnlyMap([
            $first,
            $last
        ]);

        $this->assertEquals('last', $map->tryGet(Dog::class)->transform(new Dog, new Map));
    }

    /** @test
     * @throws NotFoundException
     */
    public function it_can_get_a_existing_transformable_from_the_map(): void {
        $transformable = new GenericTreeTransformable(Dog::class, fn() => [], fn() => []);

        $map = new TreeTransformableTagReadOnlyMap([
            $transformable
        ]);

        $this->assertEquals($transformable, $map->tryGet(Dog::class));
        $this->assertEquals($transformable, $map->getOrDefault(Dog::class));
    }

    /** @test
     * @throws NotFoundException
     */
    public function it_throws_a_not_found_exception_when_a_key_is_not_found(): void {
        $this->expectException(NotFoundException::class);

        $map = new TreeTransformableTagReadOnlyMap([]);

        $map->tryGet(Dog::class);
    }

    /** @test */
    public function it_can_get_a_default_value_if_a_key_is_not_found(): void {
        $this->expectException(TypeError::class);
        $default = new GenericTreeTransformable('test', fn() => [], fn() => []);
        $map = new TreeTransformableTagReadOnlyMap([]);

        $this->assertEquals('default', $map->getOrDefault(Dog::class, 'default'));
        $this->assertEquals($default, $map->getOrDefault(Dog::class, $default));
    }
}