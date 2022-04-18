<?php

use Classes\Dog;
use Jlvn\TreeTransform\GenericTreeTransformable;
use Jlvn\TreeTransform\Map;
use Jlvn\TreeTransform\TreeTransformableInterface;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @covers \Jlvn\TreeTransform\Map
 */
class GenericTreeTransformableTest extends TestCase
{
    private TreeTransformableInterface $transformable;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transformable = new GenericTreeTransformable(
            Dog::class,
            fn() => [
                'test'
            ],
            fn() => new StdClass);
    }

    /** @test */
    public function it_returns_result_of_provided_callable_on_get_branches(): void {
        $this->assertEquals(['test'], $this->transformable->getBranches(null));
    }

    /** @test */
    public function it_returns_provided_type_on_get_type(): void {
        $this->assertEquals(Dog::class, $this->transformable->getType());
    }

    /** @test */
    public function it_returns_result_of_provided_callable_on_transform(): void {
        $trunk = new stdClass;
        $this->assertEquals($trunk, $this->transformable->transform($trunk, new Map));
    }
}